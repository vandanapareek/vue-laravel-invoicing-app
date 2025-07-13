<template>
  <InvoiceLayout>
    <header class="invoice-header">
      <div>
        <h1>Invoices</h1>
        <p class="invoice-count">
          <span v-if="filteredInvoices.length === 0">No Invoices</span>
          <span v-else>There are {{ filteredInvoices.length }} total invoices</span>
        </p>
      </div>
      <div class="invoice-header-actions">
        <div class="filter-group">
          <div class="status-select-wrapper">
            <select id="status-filter" v-model="filter">
              <option v-if="filter === ''" disabled value="">Filter By Status</option>
              <option v-if="filter !== ''" value="all">All</option>
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
            </select>
            <img :src="iconArrowDown" alt="Dropdown" class="arrow-down-icon" />
          </div>
        </div>
        <button class="new-invoice-btn" @click="goToNew">
          <span class="plus-icon"><img :src="iconPlus" alt="+" /></span>
          New Invoice
        </button>
      </div>
    </header>
    <section class="invoice-list">
      <div v-for="invoice in filteredInvoices" :key="invoice.id" class="invoice-card">
        <div class="invoice-id">#{{ invoice.id }}</div>
        <div class="invoice-due">
          <div class="due-content" :title="isOverdue(invoice) ? 'Payment is overdue' : ''">
            <span v-if="isOverdue(invoice)" class="overdue-warning">
              <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="#ff9800"/><text x="12" y="16" text-anchor="middle" font-size="14" fill="#fff">!</text></svg>
            </span>
            <span class="due-text">Due {{ formatDate(invoice.paymentDue) }}</span>
          </div>
        </div>
        <div class="invoice-client">
          <div class="client-content">
            <TruncatedText :text="invoice.clientName" :maxLength="20" />
          </div>
        </div>
        <div class="invoice-total">
          <div class="total-content">
            <span>£</span>
            <TruncatedText :text="formatAmount(invoice.total)" :maxLength="12" />
          </div>
        </div>
        <div class="invoice-status">
          <StatusBadge :status="invoice.status" />
        </div>
        <button class="invoice-arrow" @click="goToInvoiceView(invoice.id)"><img :src="iconArrowRight" alt=">" /></button>
      </div>
      <div v-if="filteredInvoices.length === 0" class="no-invoices">
        <img :src="illustrationEmpty" alt="No invoices" style="width:120px;display:block;margin:0 auto 16px;" />
        There is nothing here.
        <p class="invoice-count"><br/><br/><span>Create an invoice by clicking the<br/><b>New Invoice</b> button and get started.</span></p>
      </div>
    </section>
  </InvoiceLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import iconPlus from '@/assets/icon-plus.svg';
import iconArrowRight from '@/assets/icon-arrow-right.svg';
import illustrationEmpty from '@/assets/illustration-empty.svg';
import iconArrowDown from '@/assets/icon-arrow-down.svg';
import InvoiceLayout from '../components/InvoiceLayout.vue';
import TruncatedText from '../components/ui/TruncatedText.vue';
import StatusBadge from '../components/ui/StatusBadge.vue';
import { API_BASE_URL } from '../api.js';

const invoices = ref([]);
const filter = ref('');
const router = useRouter();

const filteredInvoices = computed(() => {
  if (filter.value === '' || filter.value === 'all') return invoices.value;
  return invoices.value.filter(inv => inv.status === filter.value);
});

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  return d.toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: '2-digit' });
};

const formatAmount = (amount) => {
  if (typeof amount === 'number') return amount.toLocaleString('en-GB', { minimumFractionDigits: 2 });
  return amount;
};

const isOverdue = (invoice) => {
  const today = new Date();
  const paymentDue = new Date(invoice.paymentDue);
  return paymentDue < today && invoice.status === 'pending';
};

const fetchInvoices = async () => {
  try {
    const res = await axios.get(`${API_BASE_URL}/api/invoices`);
    invoices.value = res.data;
  } catch (e) {
    invoices.value = [];
  }
};

const goToNew = () => {
  router.push('/new');
};

function goToInvoiceView(id) {
  router.push({ name: 'InvoiceView', params: { id } });
}

window.addEventListener('online', fetchInvoices);

onMounted(fetchInvoices);
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.invoice-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 40px;
  width: 100%;
}
.invoice-header h1 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0 0 8px 0;
}
.invoice-count {
  color: #dfe3fa;
  font-size: 0.8rem;
  margin: 0;
}
.invoice-header-actions {
  display: flex;
  align-items: center;
  gap: 24px;
}
.filter-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  color: #fff;
  font-size: 1rem;
}
.filter-group label {
  margin-bottom: 4px;
}
.filter-group select {
  background: #252945;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 6px 16px;
  font-size: 1rem;
}
.new-invoice-btn {
  display: flex;
  align-items: center;
  background: #7c5dfa;
  color: #fff;
  border: none;
  border-radius: 24px;
  font-weight: 700;
  font-size: 0.8rem;
  padding: 8px 11px;
  cursor: pointer;
  transition: background 0.2s;
  box-shadow: 0 2px 8px rgba(124,93,250,0.15);
}
.new-invoice-btn:hover {
  background: #9277ff;
}
.plus-icon {
  background: #fff;
  border-radius: 50%;
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}
.plus-icon img {
  width: 12px;
  height: 12px;
}
.invoice-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
}
.invoice-card {
  background: #1e2139;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(30,33,57,0.15);
  font-size: 0.8rem;
  width: 100%;
  display: grid;
  grid-template-columns: 120px 1fr 120px 120px 100px 40px;
  gap: 24px;
  align-items: center;
  padding: 18px;
  position: relative;
}
.invoice-id {
  font-weight: 700;
  color: #fff;
  font-size: 0.8rem;
  font-family: 'Fira Mono', 'Consolas', monospace;
}
.invoice-due, .invoice-client {
  color: #dfe3fa;
  min-width: 120px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.invoice-due {
  display: flex;
  align-items: center;
  gap: 6px;
  position: relative;
}
.due-content {
  position: relative;
  display: flex;
  align-items: center;
  cursor: help;
}
.due-text {
  flex: 1;
}
.overdue-warning {
  display: flex;
  align-items: center;
  flex-shrink: 0;
  position: absolute;
  left: -20px;
  top: 50%;
  transform: translateY(-50%);
}
.invoice-client {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.client-content, .total-content {
  display: flex;
  align-items: center;
  width: 100%;
}
.invoice-total {
  font-weight: 700;
  color: #fff;
  font-size: 0.8rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.invoice-status {
  display: flex;
  align-items: center;
  gap: 8px;
  border-radius: 24px;
  font-weight: 700;
  font-size: 1rem;
  padding: 8px 20px;
  width: 100px;
  justify-content: center;
  justify-self: end;
}
.invoice-status.paid {
  background: rgba(51, 214, 159, 0.1);
  color: #33d69f;
}
.invoice-status.pending {
  background: rgba(255, 143, 0, 0.1);
  color: #ff8f00;
}
.invoice-status.draft {
  background: #373b53;
  color: #dfe3fa;
}
.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 4px;
  flex-shrink: 0;
}
.invoice-status.paid .status-dot {
  background: #33d69f;
}
.invoice-status.pending .status-dot {
  background: #ff8f00;
  box-shadow: 0 0 0 2px rgba(255, 143, 0, 0.2);
}
.invoice-status.draft .status-dot {
  background: #dfe3fa;
}
.status-label {
  font-size: 0.8rem;
}
.invoice-arrow {
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-self: end;
}
.invoice-arrow img {
  width: 8px;
  height: 11px;
}
.no-invoices {
  text-align: center;
  color: #dfe3fa;
  padding: 60px 0;
}
.offline-msg {
  color: #ff4d4f;
  margin-top: 24px;
  text-align: center;
}
.status-select-wrapper {
  position: relative;
  display: inline-block;
}
.status-select-wrapper select {
  padding-right: 2em;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: none;
  color: inherit;
  border: none;
  cursor: pointer;
  outline: none;
}
.status-select-wrapper select:focus,
.status-select-wrapper select:active {
  border: none;
  outline: none;
  box-shadow: none;
}
.status-select-wrapper select::-ms-expand {
  display: none;
}
.arrow-down-icon {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 1em;
  pointer-events: none;
}
@media (max-width: 900px) {
  .invoice-card {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto auto;
    gap: 12px;
    padding: 20px 12px;
  }
  .invoice-id {
    grid-column: 1;
  }
  .invoice-due {
    grid-column: 2;
  }
  .invoice-client {
    grid-column: 1;
  }
  .invoice-total {
    grid-column: 2;
  }
  .invoice-status {
    grid-column: 1;
    justify-self: start;
  }
  .invoice-arrow {
    grid-column: 2;
    justify-self: end;
  }
  .invoice-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
}
@media (max-width: 600px) {
  .invoice-header h1 {
    font-size: 2rem;
  }
  .invoice-card {
    font-size: 1rem;
    padding: 14px 6px;
  }
}
</style> 