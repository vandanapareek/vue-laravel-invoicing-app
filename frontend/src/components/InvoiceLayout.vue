<template>
  <div class="invoice-app-bg">
    <aside class="sidebar">
      <div class="sidebar-logo">
        <img :src="logo" alt="Logo" />
      </div>
      <div class="sidebar-bottom">
        <img :src="avatar" class="sidebar-avatar" alt="Avatar" />
      </div>
    </aside>
    <main class="invoice-main">
      <slot />
    </main>
  </div>
</template>

<script setup>
import logo from '@/assets/logo.svg';
import avatar from '@/assets/image-avatar.jpg';
</script>

<style scoped>
.invoice-app-bg {
  display: flex;
  min-height: 100vh;
  background: #141625;
  color: #fff;
  font-family: 'Inter', Arial, sans-serif;
  max-width: 1200px;
  margin: 0 auto;
}
.sidebar {
  width: 100px;
  background: #1e2139;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  border-top-right-radius: 32px;
  border-bottom-right-radius: 32px;
}
.sidebar-logo {
  background: #7c5dfa;
  border-radius: 0 20px 20px 0;
  width: 90px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 32px;
}
.sidebar-logo img {
  width: 32px;
  height: 32px;
}
.sidebar-bottom {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.sidebar-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #fff;
}
.invoice-main {
  margin:0 auto;
  max-width: 850px;
  width: 100%;
  padding: 48px 16px 48px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
@media (max-width: 600px) {
  .invoice-app-bg {
    flex-direction: column;
    max-width: 100vw;
    width: 100vw;
    overflow-x: hidden;
  }
  .sidebar {
    width: 100vw;
    flex-direction: row;
    height: 60px;
    border-radius: 0;
    justify-content: space-between;
    align-items: center;
    padding: 0 8px;
  }
  .sidebar-logo {
    width: 48px;
    height: 48px;
    margin-bottom: 0;
    border-radius: 12px;
  }
  .sidebar-bottom {
    flex-direction: row;
    align-items: center;
  }
  .sidebar-avatar {
    width: 32px;
    height: 32px;
  }
  .invoice-main {
    max-width: 100vw;
    width: 100vw;
    padding: 16px 0 16px 0;
    box-sizing: border-box;
  }
}
</style>
