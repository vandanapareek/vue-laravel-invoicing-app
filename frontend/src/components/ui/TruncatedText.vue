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
  return props.text.length > props.maxLength;
});

const displayText = computed(() => {
  if (shouldTruncate.value) {
    return props.text.substring(0, props.maxLength) + props.suffix;
  }
  return props.text;
});

const fullText = computed(() => {
  return props.text;
});
</script>

<style scoped>
span {
  cursor: default;
}
</style> 