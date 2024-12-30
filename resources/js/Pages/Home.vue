<script setup lang="ts">
import { onMounted } from "vue";
import { isTokenInLocalStorage } from "../utils";
import { router as Inertia } from '@inertiajs/vue3';

const handleOptionClick = (type: 'vendor' | 'buyer') => {
  localStorage.setItem('choice', type);
  Inertia.visit('/login');
}
</script>

<template>
  <div style="display: flex; place-items: center;" >
    <div class="landing-container">
    <h1 class="text-4xl font-bold mb-6">Bienvenue sur notre plateforme</h1>
    
    <div class="options-container">
      <div class="option-card" @click="handleOptionClick('vendor')">
        <div class="option-icon">🏪</div>
        <h2 class="option-title">Je suis vendeur</h2>
        <p class="option-description">
          Gérez votre boutique en ligne, ajoutez des produits et développez votre activité
        </p>
      </div>

      <div class="option-card" @click="handleOptionClick('buyer')">
        <div class="option-icon">🛍️</div>
        <h2 class="option-title">Je suis acheteur</h2>
        <p class="option-description">
          Découvrez nos produits, comparez les prix et effectuez vos achats en toute sécurité
        </p>
      </div>
    </div>
    </div>
  </div>
  
</template>