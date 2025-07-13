<template>
  <InvoiceLayout>
    <div class="form-content-wrapper">
      <div class="go-back-row">
        <Button variant="ghost" @click="goBack">
          <img :src="iconArrowLeft" alt="Back" class="arrow" /> Go back
        </Button>
      </div>
      <h1 class="form-title">
        <span v-if="isEdit">Edit</span>
        <span v-else>New</span>
        <span class="invoice-id" v-if="isEdit">#{{ route.params.id }}</span>
        <span v-else> Invoice</span>
      </h1>
      <form ref="invoiceForm" @submit.prevent="handleSubmit">
        <AddressInput v-model="form.senderAddress" sectionLabel="Bill From" />
        <div class="section-label">Bill To</div>
        <div class="form-group">
          <label>Client's Name</label>
          <input v-model="form.clientName" required maxlength="100" />
        </div>
        <div class="form-group">
          <label>Client's Email</label>
          <input v-model="form.clientEmail" type="email" required maxlength="254" placeholder="e.g. email@example.com" />
        </div>
        <AddressInput v-model="form.clientAddress" />
        <div class="form-row">
          <div class="form-group">
            <label>Invoice Date</label>
            <div class="date-input-wrapper">
              <input type="date" v-model="form.createdAt" required @click="openDatePicker" />
              <img :src="iconCalendar" alt="Calendar" class="calendar-icon" />
            </div>
          </div>
          <div class="form-group payment-terms-group">
            <label>Payment Terms</label>
            <div class="select-arrow-wrapper">
              <select v-model="form.paymentTerms" required>
                <option value="1">Net 1 Day</option>
                <option value="7">Net 7 Days</option>
                <option value="14">Net 14 Days</option>
                <option value="30">Net 30 Days</option>
              </select>
              <img :src="iconArrowDown" alt="Dropdown" class="down-arrow-icon" />
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Project Description</label>
          <input v-model="form.description" required maxlength="1000" placeholder="e.g. Graphic Designer Service" />
        </div>
        <InvoiceItemListInput v-model="form.items" />
        <div class="form-actions">
          <div v-if="!isEdit" class="form-actions-left">
            <Button type="button" variant="secondary" @click="onDiscard">Discard</Button>
          </div>
          <div class="form-actions-right">
            <Button v-if="!isEdit" type="button" variant="secondary" @click="saveAsDraft">Save as Draft</Button>
            <Button v-if="isEdit" type="button" variant="secondary" @click="onCancel">Cancel</Button>
            <Button v-if="isEdit" type="submit" variant="primary">Save Changes</Button>
            <Button v-else type="submit" variant="primary">Save & Send</Button>
          </div>
        </div>
      </form>
    </div>
  </InvoiceLayout>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import InvoiceLayout from '../components/InvoiceLayout.vue';
import iconArrowDown from '@/assets/icon-arrow-down.svg';
import iconArrowLeft from '@/assets/icon-arrow-left.svg';
import iconCalendar from '@/assets/icon-calendar.svg';
import Button from '@/components/ui/Button.vue';
import { API_BASE_URL } from '../api.js';
import AddressInput from '../components/invoices/AddressInput.vue';
import InvoiceItemListInput from '../components/invoices/InvoiceItemListInput.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);
let originalForm = null;

const today = new Date().toISOString().split('T')[0];

const form = ref({
  senderAddress: { street: '', city: '', postCode: '', country: '' },
  clientName: '',
  clientEmail: '',
  clientAddress: { street: '', city: '', postCode: '', country: '' },
  createdAt: today, // set to today by default
  paymentTerms: '30',
  description: '',
  items: [],
  status: 'pending',
});

const invoiceForm = ref(null);
const itemsError = ref('');

const fetchInvoice = async (id) => {
  try {
    const res = await axios.get(`${API_BASE_URL}/api/invoices/${id}`);
    const invoice = res.data;
    
    // Check if invoice is paid - if so, redirect to view page
    if (invoice.status === 'paid') {
      router.push({ name: 'InvoiceView', params: { id } });
      return;
    }
    
    form.value = {
      id: invoice.id,
      senderAddress: invoice.senderAddress || { street: '', city: '', postCode: '', country: '' },
      clientName: invoice.clientName,
      clientEmail: invoice.clientEmail,
      clientAddress: invoice.clientAddress || { street: '', city: '', postCode: '', country: '' },
      createdAt: invoice.createdAt ? invoice.createdAt.split('T')[0] : '',
      paymentTerms: invoice.paymentTerms ? String(invoice.paymentTerms) : '30',
      description: invoice.description || '',
      items: Array.isArray(invoice.items) ? invoice.items : (invoice.items ? JSON.parse(invoice.items) : []),
      status: invoice.status || 'pending',
    };
    originalForm = JSON.parse(JSON.stringify(form.value));
  } catch (error) {
    console.error('Error fetching invoice:', error);
    router.push('/');
  }
};

const validateAllFields = () => {
  // Clear previous items error
  itemsError.value = '';
  // Only require these fields:
  const requiredFields = [
    'clientName', 'clientEmail', 'createdAt', 'paymentTerms', 'description'
  ];
  for (const field of requiredFields) {
    const parts = field.split('.');
    let val = form.value;
    for (const p of parts) val = val[p];
    if (!val) return false;
  }
  // Check for at least one item
  if (!form.value.items.length) {
    itemsError.value = 'Please add at least one item to the invoice.';
    return false;
  }
  // Check that all items have required fields
  for (const item of form.value.items) {
    if (!item.name || !item.quantity || item.price === '' || item.price === null || item.price === undefined) {
      itemsError.value = 'All items must have a name, quantity, and price.';
      return false;
    }
  }
  return true;
};

const setAllRequired = (required) => {
  if (!invoiceForm.value) return;
  const elements = invoiceForm.value.querySelectorAll('[required]');
  elements.forEach(el => {
    if (required) {
      el.setAttribute('required', 'required');
    } else {
      el.removeAttribute('required');
    }
  });
};

const saveAsDraft = async () => {
  setAllRequired(false);
  if (invoiceForm.value && !invoiceForm.value.reportValidity()) {
    setAllRequired(true);
    return;
  }
  setAllRequired(true);
  form.value.status = 'draft';
  await submitForm();
};

const handleSubmit = async (e) => {
  // Edit mode: Save Changes
  if (isEdit.value) {
    if (!validateAllFields()) {
      alert(itemsError.value || 'Please fill all required fields.');
      return;
    }
    // If status is draft, set to pending
    if (form.value.status === 'draft') {
      form.value.status = 'pending';
    }
    await submitForm();
  } else {
    // Create mode: Save & Send
    if (!validateAllFields()) {
      alert(itemsError.value || 'Please fill all required fields.');
      return;
    }
    form.value.status = 'pending';
    await submitForm();
  }
};

const onCancel = () => {
  router.push('/');
};

const DRAFT_KEY = 'new_invoice_draft';

// Save draft to localStorage on change (create mode only)
watch(form, (val) => {
  if (!isEdit.value) {
    localStorage.setItem(DRAFT_KEY, JSON.stringify(val));
  }
}, { deep: true });

// Restore draft from localStorage on mount (create mode only)
onMounted(() => {
  if (route.params.id) {
    fetchInvoice(route.params.id);
  } else {
    const draft = localStorage.getItem(DRAFT_KEY);
    if (draft) {
      try {
        const parsed = JSON.parse(draft);
        Object.assign(form.value, parsed);
      } catch {}
    }
  }
});

// Clear draft from localStorage on submit (create mode only)
async function submitForm() {
  const itemsForPayload = form.value.items.map(({ name, quantity, price }) => ({
    name,
    quantity: quantity === '' ? 0 : Number(quantity),
    price: price === '' ? 0 : Number(price),
  }));
  const payload = {
    senderAddress: form.value.senderAddress,
    clientName: form.value.clientName,
    clientEmail: form.value.clientEmail,
    clientAddress: form.value.clientAddress,
    createdAt: form.value.createdAt,
    paymentTerms: Number(form.value.paymentTerms),
    description: form.value.description,
    items: itemsForPayload,
    status: form.value.status,
  };
  
  try {
    if (route.params.id) {
      await axios.put(`${API_BASE_URL}/api/invoices/${route.params.id}`, payload);
    } else {
      await axios.post(`${API_BASE_URL}/api/invoices`, payload);
      localStorage.removeItem(DRAFT_KEY);
    }
    router.push('/');
  } catch (error) {
    if (error.response && error.response.status === 403) {
      alert(error.response.data.error || 'This invoice has been marked as paid and cannot be edited.');
      router.push({ name: 'InvoiceView', params: { id: route.params.id } });
    } else {
      alert('An error occurred while saving the invoice.');
    }
  }
}

const onDiscard = () => {
  localStorage.removeItem(DRAFT_KEY);
  router.push('/');
};

const goBack = () => {
  router.back();
};

const openDatePicker = (event) => {
  event.target.focus();
  event.target.showPicker();
};
</script>

<style scoped>
.form-content-wrapper {
  width: 100%;
  max-width: 540px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
}
.go-back-row,
.form-title {
  margin-left: 0;
}
.form-title {
  font-size: 1.6rem;
  font-weight: 700;
  margin-bottom: 32px;
  color: #fff;
}
.invoice-id {
  color: #dfe3fa;
  font-weight: 700;
  margin-left: 8px;
}
.section-label {
  color: #7c5dfa;
  font-weight: 700;
  margin: 32px 0 12px 0;
  font-size: 1.1rem;
}
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 18px;
}
.form-group label {
  color: #dfe3fa;
  font-size: 0.9rem;
  margin-bottom: 6px;
  font-weight: 500;
}
.form-group input,
.form-group select {
  background: #252945;
  border: none;
  border-radius: 8px;
  padding: 12px 16px;
  color: #fff;
  font-size: 0.9rem;
  font-family: inherit;
  font-weight: 700;
  outline: none;
  margin-bottom: 0;
}
.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 18px;
  width: 100%;
}
.form-row .form-group {
  flex: 1 1 0;
  min-width: 0;
}
.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  margin-top: 32px;
}
.form-actions-left {
  flex: 1;
  display: flex;
  justify-content: flex-start;
}
.form-actions-right {
  flex: 2;
  display: flex;
  justify-content: flex-end;
  gap: 16px;
}
.go-back-row {
  margin-bottom: 32px;
}
.go-back-row .arrow {
  font-size: 1.2rem;
  margin-right: 8px;
  margin-bottom: 1px;
}
.payment-terms-group .select-arrow-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.payment-terms-group select {
  cursor: pointer;
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  padding-right: 2em;
}
.payment-terms-group .down-arrow-icon {
  position: absolute;
  right: 1.2em;
  pointer-events: none;
}
.date-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.date-input-wrapper input[type="date"] {
  cursor: pointer;
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  padding-right: 2em;
}
.date-input-wrapper .calendar-icon {
  position: absolute;
  right: 1.2em;
  pointer-events: none;
  width: 16px;
  height: 16px;
}
/* Hide default browser calendar icon */
input[type="date"]::-webkit-calendar-picker-indicator {
  display: none;
}
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-outer-spin-button {
  display: none;
}
@media (max-width: 600px) {
  .form-content-wrapper {
    max-width: 100vw;
    padding: 0 8px;
    box-sizing: border-box;
  }
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  .form-group input,
  .form-group select {
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
  }
}
</style> 