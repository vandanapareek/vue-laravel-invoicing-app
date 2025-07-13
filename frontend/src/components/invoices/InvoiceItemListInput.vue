<template>
  <div class="item-list-section">
    <div class="item-list-label">Item List</div>
    <div class="item-list-table">
      <div class="item-list-header">
        <div>Item Name</div>
        <div>Qty.</div>
        <div>Price</div>
        <div>Total</div>
        <div></div>
      </div>
      <div v-for="(item, idx) in localItems" :key="idx" class="item-list-row">
        <input v-model="item.name" required class="form-input" maxlength="60" />
        <input v-model="item.quantity" type="number" min="1" max="999999" step="1" @input="updateItemTotal(idx)" required class="form-input item-quantity" />
        <input v-model="item.price" type="number" min="0" max="999999.99" step="0.01" @input="updateItemTotal(idx)" required class="form-input item-price" />
        <input :value="(Number(item.quantity) * Number(item.price) || 0).toFixed(2)" readonly class="form-input item-total" />
        <img :src="iconDelete" alt="Delete" class="delete-icon" @click="removeItem(idx)" />
      </div>
    </div>
    <Button type="button" variant="secondary" class="add-item-btn" @click="addItem">
      <img :src="iconPlus" alt="Add" class="plus-icon" /> Add New Item
    </Button>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';
import Button from '@/components/ui/Button.vue';
import iconDelete from '@/assets/icon-delete.svg';
import iconPlus from '@/assets/icon-plus.svg';

const props = defineProps({
  modelValue: {
    type: Array,
    required: true
  }
});
const emit = defineEmits(['update:modelValue']);

const localItems = reactive(props.modelValue.map(item => ({ ...item })));

watch(
  () => props.modelValue,
  (val) => {
    if (val && val.length !== localItems.length) {
      // Sync length
      localItems.splice(0, localItems.length, ...val.map(item => ({ ...item })));
    }
  },
  { deep: true }
);

watch(
  localItems,
  (val) => {
    emit('update:modelValue', val.map(item => ({ ...item }))); 
  },
  { deep: true }
);

function addItem() {
  localItems.push({ name: '', quantity: '', price: '', total: 0 });
}
function removeItem(idx) {
  localItems.splice(idx, 1);
}
function updateItemTotal(idx) {
  const item = localItems[idx];
  item.quantity = item.quantity === '' ? '' : Number(item.quantity);
  item.price = item.price === '' ? '' : Number(item.price);
  item.total = (Number(item.quantity) || 0) * (Number(item.price) || 0);
}
</script>

<style scoped>
.item-list-section {
  border-radius: 8px;
  margin-top: 40px;
  margin-bottom: 24px;
}
.item-list-label {
  color: #7c5dfa;
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 16px;
}
.item-list-table {
  border-collapse: collapse;
  width: 100%;
}
.item-list-header {
  display: grid;
  grid-template-columns: 1fr 0.5fr 0.5fr 0.5fr 0.2fr;
  gap: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #494e6e;
  color: #dfe3fa;
  font-weight: 700;
  font-size: 0.95rem;
}
.item-list-row {
  display: grid;
  grid-template-columns: 1fr 0.5fr 0.5fr 0.5fr 0.2fr;
  gap: 16px;
  padding: 16px 0;
}
.item-list-row:last-child {
  border-bottom: none;
}
.item-list-row input.form-input {
  background: #252945;
  border: none;
  border-radius: 8px;
  padding: 12px 16px;
  color: #fff;
  font-size: 0.9rem;
  font-family: inherit;
  font-weight: 700;
  outline: none;
  width: 80%;
  margin-bottom: 0;
}
.item-total{
  background: none !important;
}
.delete-icon {
  width: 1.2em;
  height: 1.2em;
  cursor: pointer;
  vertical-align: middle;
  padding-left: 12px;
  padding-top: 10px;
}
.add-item-btn {
  width: 100%;
  margin-top: 16px;
}
.plus-icon {
  width: 11px;
  height: 11px;
  filter: brightness(0) invert(1);
}
.item-quantity, .item-price {
  width: 50% !important;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="number"] {
  -moz-appearance: textfield;
}


@media (max-width: 600px) {
  .item-list-header,
  .item-list-row {
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }
  .item-list-row input.form-input {
    width: 100%;
  }
}
</style> 