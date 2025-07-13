<template>
  <button
    :type="type"
    :class="buttonClasses"
    :disabled="disabled"
    @click="$emit('click', $event)"
    v-bind="$attrs"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

defineEmits(['click']);

const buttonClasses = computed(() => [
  'btn',
  `btn--${props.variant}`,
  `btn--${props.size}`,
  { 'btn--disabled': props.disabled }
]);
</script>

<style scoped>
.btn {
  border: none;
  border-radius: 24px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  outline: none;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Primary Button (Save buttons) */
.btn--primary {
  background: #7c5dfa;
  color: #fff;
}

.btn--primary:hover:not(:disabled) {
  background: #9277ff;
}

/* Secondary Button (Cancel buttons) */
.btn--secondary {
  background: #252945;
  color: #dfe3fa;
}

.btn--secondary:hover:not(:disabled) {
  background: #373b53;
}

/* Danger Button (Delete buttons) */
.btn--danger {
  background: #ec5757;
  color: #fff;
}

.btn--danger:hover:not(:disabled) {
  background: #ff9797;
}

/* Ghost Button (Go back button) */
.btn--ghost {
  background: none;
  color: #7c5dfa;
  padding: 0;
  border-radius: 0;
}

.btn--ghost:hover:not(:disabled) {
  background: none;
  color: #9277ff;
}

/* Size variants */
.btn--small {
  padding: 8px 16px;
  font-size: 0.8rem;
}

.btn--medium {
  padding: 12px 32px;
  font-size: 0.9rem;
}

.btn--large {
  padding: 16px 40px;
  font-size: 1rem;
}

/* Special case for ghost button - no padding */
.btn--ghost.btn--small,
.btn--ghost.btn--medium,
.btn--ghost.btn--large {
  padding: 0;
}
</style> 