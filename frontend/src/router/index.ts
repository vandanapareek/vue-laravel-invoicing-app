import { createRouter, createWebHistory } from 'vue-router'
import InvoiceList from '../views/InvoiceList.vue';
import InvoiceForm from '../views/InvoiceForm.vue';
import InvoiceView from '../views/InvoiceView.vue';

const routes = [
  { path: '/', name: 'InvoiceList', component: InvoiceList },
  { path: '/new', name: 'InvoiceNew', component: InvoiceForm },
  { path: '/edit/:id', name: 'InvoiceEdit', component: InvoiceForm, props: true },
  { path: '/invoice/:id', name: 'InvoiceView', component: InvoiceView, props: true },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
