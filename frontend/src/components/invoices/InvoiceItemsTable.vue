<template>
  <div class="invoice-items-table">
    <div class="table-header">
      <div>Item Name</div>
      <div class="qty-col">QTY.</div>
      <div class="price-col">Price</div>
      <div class="total-col">Total</div>
    </div>
    <div v-for="(item, idx) in items" :key="idx" class="table-row">
      <div class="strong item-name">
        <TruncatedText :text="item.name" :maxLength="20" />
      </div>
      <div class="qty-col">{{ item.quantity }}</div>
      <div class="price-col">£ {{ Number(item.price).toFixed(2) }}</div>
      <div class="total-col strong">
        <TruncatedText :text="`£ ${(item.quantity * item.price).toFixed(2)}`" :maxLength="10" />
      </div>
    </div>
    <div class="table-footer">
      <div class="amount-due-label">Amount Due</div>
      <div class="amount-due-value">£ {{ total ? Number(total).toFixed(2) : '0.00' }}</div>
    </div>
  </div>
</template>

<script setup>
import TruncatedText from '../ui/TruncatedText.vue';
const props = defineProps({
  items: {
    type: Array,
    required: true
  },
  total: {
    type: [Number, String],
    required: true
  }
});
</script>

<style scoped>
.invoice-items-table {
  background: #1e2139;
  border-radius: 8px 8px 0 0;
  margin-top: 32px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(30,33,57,0.10);
}
.table-header {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr;
  padding: 18px 40px;
  column-gap: 24px;
  color: #dfe3fa;
  font-size: 0.8rem;
  background: #252945;
  border-radius: 8px 8px 0 0;
}
.table-row {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr;
  padding: 16px 40px;
  column-gap: 24px;
  color: #fff;
  font-size: 0.8rem;
  background: #252945;
  border-bottom: 1px solid #252945;
  align-items: center;
}
.item-name {
  font-weight: 700;
}
.qty-col, .price-col {
  text-align: center;
}
.total-col {
  text-align: right;
}
.table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0c0e16;
  padding: 28px 32px;
  border-radius: 0 0 8px 8px;
  margin-top: 0;
}
.amount-due-label {
  color: #dfe3fa;
  font-size: 0.8rem;
}
.payment-due-label {
  margin-top: 25px;
}
.amount-due-value {
  color: #fff;
  font-size: 1.5rem;
  font-weight: 700;
}
</style> 