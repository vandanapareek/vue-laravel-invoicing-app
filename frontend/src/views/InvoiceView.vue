<template>
  <InvoiceLayout>
    <div class="invoice-view-container">
      <div class="go-back-row">
        <Button variant="ghost" @click="goBack">
          <img :src="iconArrowLeft" alt="Back" class="arrow" /> Go back
        </Button>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else-if="invoice">
        <!-- Status Bar with Action Buttons -->
        <div class="status-bar">
          <div class="status-label">
            Status
            <StatusBadge :status="invoice.status" />
          </div>
          <div class="action-buttons">
            <Button variant="secondary" @click="goToEdit" v-if="invoice.status !== 'paid'">Edit</Button>
            <Button variant="danger" @click="openDeleteModal">Delete</Button>
            <div class="tooltip-wrapper" v-if="invoice.status === 'pending'">
              <Button
                variant="primary"
                @click="handleMarkAsPaid"
                tabindex="0"
                type="button"
                style="position: relative;"
              >
                Mark as Paid
              </Button>
            </div>
          </div>
        </div>
        <!-- Invoice Card -->
        <div class="invoice-card">
          <div class="invoice-header-row">
            <div class="invoice-id-desc">
              <div class="invoice-id"><span class="hash-symbol">#</span>{{ invoice.id }}</div>
              <div class="invoice-desc">
                <TruncatedText :text="invoice.description || invoice.clientName || ''" :maxLength="30" />
              </div>
            </div>
            <div class="invoice-address">
              <AddressDisplay :address="invoice.senderAddress" />
            </div>
          </div>
          <div class="invoice-details-row">
            <div class="invoice-dates">
              <div class="label">Invoice Date</div>
              <div class="value strong">{{ formatDate(invoice.createdAt) }}</div>
              <div class="label payment-due-label">Payment Due</div>
              <div class="value strong">{{ formatDate(invoice.paymentDue) }}</div>
            </div>
            <div class="invoice-billto">
              <div class="label">Bill To</div>
              <div class="value strong">
                <TruncatedText :text="invoice.clientName || '—'"  :maxLength="20" />
              </div>
              <div class="value bill-to-address" v-if="invoice.clientAddress">
                <AddressDisplay :address="invoice.clientAddress" textAlign="left" />
              </div>
            </div>
            <div class="invoice-sentto">
              <div class="label">Sent to</div>
              <div class="value strong">
                <TruncatedText :text="invoice.clientEmail || '—'" :maxLength="30" />
              </div>
            </div>
          </div>
          <!-- Item List Table -->
          <InvoiceItemsTable :items="parsedItems" :total="invoice.total" />
        </div>
      </div>
    </div>
    <ConfirmModal
      v-if="showDeleteModal"
      :message="`Are you sure you want to delete invoice #${invoice.id}? This action cannot be undone.`"
      @confirm="handleDelete"
      @cancel="closeDeleteModal"
    />
  </InvoiceLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import InvoiceLayout from '../components/InvoiceLayout.vue';
import iconArrowLeft from '@/assets/icon-arrow-left.svg';
import Button from '@/components/ui/Button.vue';
import TruncatedText from '../components/ui/TruncatedText.vue';
import AddressDisplay from '../components/invoices/AddressDisplay.vue';
import StatusBadge from '../components/ui/StatusBadge.vue';
import InvoiceItemsTable from '../components/invoices/InvoiceItemsTable.vue';
import { API_BASE_URL } from '../api.js';
import ConfirmModal from '../components/ui/ConfirmModal.vue';

const route = useRoute();
const router = useRouter();
const invoice = ref(null);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);

const id = route.params.id;

async function fetchInvoice() {
  loading.value = true;
  try {
    const url = `${API_BASE_URL}/api/invoices/${id}`;
    const res = await axios.get(url);
    invoice.value = res.data;
    error.value = null;
  } catch (e) {
    error.value = 'Invoice not found.';
  } finally {
    loading.value = false;
  }
}

function goBack() {
  router.back();
}

function goToEdit() {
  router.push({ name: 'InvoiceEdit', params: { id } });
}

async function markAsPaid() {
  await axios.post(`${API_BASE_URL}/api/invoices/${id}/pay`);
  fetchInvoice();
}

function handleMarkAsPaid() {
  markAsPaid();
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

const parsedItems = computed(() => {
  if (!invoice.value || !invoice.value.items) return [];
  try {
    return typeof invoice.value.items === 'string' ? JSON.parse(invoice.value.items) : invoice.value.items;
  } catch {
    return [];
  }
});

function openDeleteModal() { showDeleteModal.value = true; }
function closeDeleteModal() { showDeleteModal.value = false; }
async function handleDelete() {
  await axios.delete(`${API_BASE_URL}/api/invoices/${id}`);
  router.push({ name: 'InvoiceList' });
  closeDeleteModal();
}

onMounted(fetchInvoice);
</script>

<style scoped>
.invoice-view-container {
  width: 100%;
  max-width: 730px;
  padding: 0;
}
.go-back-row {
  margin-bottom: 24px;
}
.go-back-row .arrow {
  font-size: 1.2rem;
  margin-right: 8px;
  margin-bottom: 2px;
}
.status-bar {
  background: #1e2139;
  border-radius: 8px;
  padding: 24px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(30,33,57,0.10);
}
.status-label {
  color: #dfe3fa;
  font-size: 0.8rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 16px;
}
.read-only-indicator {
  color: #dfe3fa;
  font-size: 0.8rem;
  font-style: italic;
  margin-left: 8px;
}
.action-buttons {
  display: flex;
  gap: 12px;
}
.invoice-card {
  background: #1e2139;
  border-radius: 12px;
  padding: 40px 40px 40px 40px;
  color: #fff;
  margin-bottom: 40px;
  box-shadow: 0 2px 8px rgba(30,33,57,0.15);
}
.invoice-header-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 32px;
}
.invoice-id-desc {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.invoice-id {
  font-weight: 700;
  font-size: 1.1rem;
  color: #fff;
  margin-bottom: 8px;
}
.payment-due-label {
  margin-top: 25px;
}
.hash-symbol {
  color: #888eb0;
  font-weight: normal;
}
.invoice-desc {
  color: #dfe3fa;
  font-size: 0.8rem;
  margin-bottom: 0;
}
.invoice-address {
  color: #dfe3fa;
  font-size: 1rem;
  text-align: right;
  white-space: pre-line;
}
.invoice-details-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 32px;
}
.invoice-dates, .invoice-billto, .invoice-sentto {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.label {
  color: #dfe3fa;
  font-size: 0.8rem;
  margin-bottom: 6px;
}
.value {
  color: #fff;
  font-size: 1rem;
}
.bill-to-address {
  font-size: 0.8rem;
  color: #dfe3fa;
}
.bill-to-address > div {
  padding-bottom: 4px;
}

.strong {
  font-weight: 700;
}
.loading, .error {
  text-align: center;
  margin: 40px 0;
  color: #fff;
}
@media (max-width: 600px) {
  .status-bar {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 8px;
  }
  .status-label {
    margin-bottom: 8px;
  }
  
  .action-buttons {
    flex-direction: column;
    width: 100%;
    gap: 8px;
  }
  .action-buttons > * {
    width: 100%;
  }
  .tooltip-wrapper {
    width: 100%;
    display: block;
  }
  .tooltip-wrapper > button {
    width: 100%;
  }
}
</style> 