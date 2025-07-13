<template>
  <div class="modal-overlay" @click.self="onCancel">
    <div class="modal-box">
      <h2 class="modal-title">{{ title }}</h2>
      <p class="modal-message">{{ message }}</p>
      <div class="modal-actions">
        <button class="btn btn-cancel" @click="onCancel">Cancel</button>
        <button class="btn btn-delete" @click="onConfirm">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, default: 'Confirm Deletion' },
  message: { type: String, required: true }
});
const emit = defineEmits(['confirm', 'cancel']);
function onConfirm() { emit('confirm'); }
function onCancel() { emit('cancel'); }
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(20, 22, 37, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-box {
  background: #23243a;
  border-radius: 16px;
  padding: 40px 36px 32px 36px;
  min-width: 340px;
  max-width: 90vw;
  box-shadow: 0 8px 32px rgba(20,22,37,0.25);
  text-align: center;
}
.modal-title {
  color: #fff;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 18px;
}
.modal-message {
  color: #dfe3fa;
  font-size: 1rem;
  margin-bottom: 32px;
}
.modal-actions {
  display: flex;
  justify-content: center;
  gap: 18px;
}
.btn {
  font-weight: 700;
  font-size: 1rem;
  border: none;
  border-radius: 24px;
  padding: 12px 32px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-cancel {
  background: #252945;
  color: #dfe3fa;
}
.btn-cancel:hover {
  background: #373b53;
}
.btn-delete {
  background: #ec5757;
  color: #fff;
}
.btn-delete:hover {
  background: #ff9797;
}
</style> 