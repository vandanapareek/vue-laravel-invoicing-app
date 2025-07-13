<template>
  <div class="address-input-section">
    <div v-if="sectionLabel" class="section-label">{{ sectionLabel }}</div>
    <div class="form-group">
      <label>Street Address</label>
      <input v-model="localAddress.street" :required="required" maxlength="100" />
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>City</label>
        <input v-model="localAddress.city" :required="required" maxlength="50" />
      </div>
      <div class="form-group">
        <label>Post Code</label>
        <input v-model="localAddress.postCode" :required="required" maxlength="10" />
      </div>
      <div class="form-group">
        <label>Country</label>
        <input v-model="localAddress.country" :required="required" maxlength="60" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, toRefs } from 'vue';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  sectionLabel: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue']);

const localAddress = reactive({
  street: props.modelValue.street || '',
  city: props.modelValue.city || '',
  postCode: props.modelValue.postCode || '',
  country: props.modelValue.country || ''
});

watch(localAddress, (val) => {
  emit('update:modelValue', { ...val });
}, { deep: true });

watch(() => props.modelValue, (val) => {
  if (val) {
    localAddress.street = val.street || '';
    localAddress.city = val.city || '';
    localAddress.postCode = val.postCode || '';
    localAddress.country = val.country || '';
  }
}, { deep: true });
</script>

<style scoped>
.address-input-section {
  margin-bottom: 18px;
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
.form-group input {
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
  margin-bottom: 0;
  width: 100%;
}
.form-row .form-group {
  flex: 1 1 0;
  min-width: 0;
}
</style> 