<template>
  <div v-if="address" :style="{ textAlign: textAlign }">
    <div v-if="address.street" class="address-line">
      <TruncatedText :text="address.street" :maxLength="25" />
    </div>
    <div v-if="address.city" class="address-line">
      <TruncatedText :text="address.city" :maxLength="15" />
    </div>
    <div v-if="address.postCode" class="address-line">
      <TruncatedText :text="address.postCode" :maxLength="10" />
    </div>
    <div v-if="address.country" class="address-line">
      <TruncatedText :text="address.country" :maxLength="15" />
    </div>
  </div>
  <div v-else>—</div>
</template>

<script setup>
import TruncatedText from '@/components/ui/TruncatedText.vue';
const props = defineProps({
  address: {
    type: Object,
    default: null
  },
  textAlign: {
    type: String,
    default: 'right'
  }
});
</script>

<style scoped>
div {
  color: #dfe3fa;
  font-size: 0.8rem;
  white-space: pre-line;
}
.address-line {
  margin-bottom: 4px;
}
.address-line:last-child {
  margin-bottom: 0;
}
</style> 