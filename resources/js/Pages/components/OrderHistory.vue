<script setup lang="ts">
import { computed } from 'vue';
import type { Order } from '../composables/useOrderHistory';

const props = defineProps<{
  orders: Order[];
  isOpen: boolean;
}>();

const emit = defineEmits<{
  'close': [];
}>();

const formatDate = (date: Date) => {
  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(new Date(date));
};

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price);
};
</script>

<template>
  <div 
    v-if="isOpen"
    class="history-overlay"
    @click="emit('close')"
  >
    <div 
      class="history-modal"
      @click.stop
    >
      <div class="history-header">
        <h2>Historique des commandes</h2>
        <button class="close-btn" @click="emit('close')">&times;</button>
      </div>

      <div class="orders" v-if="orders.length">
        <div 
          v-for="order in orders" 
          :key="order.id"
          class="order"
        >
          <div class="order-header">
            <span class="order-date">{{ formatDate(order.date) }}</span>
            <span class="order-total">{{ formatPrice(order.total) }}</span>
          </div>
          
          <div class="order-items">
            <div 
              v-for="item in order.items" 
              :key="item.product.id"
              class="order-item"
            >
              <img :src="item.product.image" :alt="item.product.title">
              <div class="item-details">
                <h4>{{ item.product.title }}</h4>
                <p>Quantite: {{ item.quantity }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-history">
        Aucune commande
      </div>
    </div>
  </div>
</template>

<style scoped>
.history-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 40;
}

.history-modal {
  background: white;
  border-radius: var(--radius);
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 2rem;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.close-btn {
  font-size: 1.5rem;
  background: none;
  border: none;
  cursor: pointer;
}

.order {
  border: 1px solid #e5e7eb;
  border-radius: var(--radius);
  margin-bottom: 1rem;
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  padding: 1rem;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.order-total {
  font-weight: 600;
  color: var(--primary);
}

.order-items {
  padding: 1rem;
}

.order-item {
  display: flex;
  gap: 1rem;
  padding: 0.5rem 0;
}

.order-item img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 0.5rem;
}

.empty-history {
  text-align: center;
  color: #6b7280;
  padding: 2rem;
}

@media (max-width: 640px) {
  .history-modal {
    width: 100%;
    height: 100%;
    max-height: 100%;
    border-radius: 0;
  }
}
</style>