<template>
  <span class="status-badge" :class="status">
    <span class="dot" :style="{ background: dotColor }"></span>
    {{ statusLabel }}
  </span>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: v => ['paid', 'pending', 'draft'].includes(v)
  }
});
const statusLabel = computed(() =>
  props.status ? props.status.charAt(0).toUpperCase() + props.status.slice(1) : ''
);
const dotColor = computed(() => {
  if (props.status === 'pending') return '#ff8f00';
  if (props.status === 'paid') return '#33d69f';
  return '#dfe3fa';
});
</script>

<style scoped>
.status-badge {
  background: #2b2e4a;
  border-radius: 6px;
  padding: 8px 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
  color: #fff;
  gap: 8px;
  min-width: 110px;
  max-width: 110px;
  width: 110px;
  box-sizing: border-box;
}
.status-badge.pending {
  color: #ff8f00;
}
.status-badge.paid {
  color: #33d69f;
}
.status-badge.draft {
  color: #dfe3fa;
}
.status-badge .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}
</style> 