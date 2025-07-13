<template>
  <span :title="shouldTruncate ? fullText : ''">
    {{ displayText }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  text: {
    type: String,
    required: true
  },
  maxLength: {
    type: Number,
    required: true
  },
  suffix: {
    type: String,
    default: '...'
  }
});

const shouldTruncate = computed(() => {
  return (props.text ?? '').length > props.maxLength;
});

const displayText = computed(() => {
  const safeText = props.text ?? '';
  if (shouldTruncate.value) {
    return safeText.substring(0, props.maxLength) + props.suffix;
  }
  return safeText;
});

const fullText = computed(() => {
  return props.text ?? '';
});
</script>

<style scoped>
span {
  cursor: default;
}
</style> 