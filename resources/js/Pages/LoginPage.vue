<script setup lang="ts">
import { onMounted, ref } from 'vue'
import axios from 'axios'
import GlobalLoader from './components/GlobalLoader.vue'
import { JWT_DATA } from '../utils/constants'
import { router as Inertia } from '@inertiajs/vue3'
import { isTokenInLocalStorage } from '../utils'

const email = ref('')
const password = ref('')
const isLoading = ref(false)

const handleSubmit = async () => {
    isLoading.value = true;
    try {
        const resp = await axios.post('/api/login', {
            email: email.value,
            password: password.value,
            type: localStorage.getItem('choice') ?? 'buyer'
        });
        if (resp.status.toString().startsWith('2')) {

            localStorage.setItem(JWT_DATA, JSON.stringify(resp.data));
            Inertia.visit(`/${localStorage.getItem('choice') ?? 'buyer'}`)

        }
        else {
            alert('Email ou mot de passe incorrecte.')
        }
    } catch (e) {
        console.log("Error occured while signing in: ");
        console.log(e);
        alert('Email ou mot de passe incorrecte.');
    }
    isLoading.value = false;
}

const goToRegister = () => {
    Inertia.visit('/register');
}

// onMounted(() => {
//     if(isTokenInLocalStorage()) {
//         Inertia.visit(`/${localStorage.getItem('choice') ?? 'buyer'}`);
//     }
// });

</script>

<template>
    <div>
        <GlobalLoader :is-loading="isLoading" />
        <div class="auth-container">
            <h1>Connexion</h1>
            <form @submit.prevent="handleSubmit" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="email" required placeholder="votre@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" v-model="password" required placeholder="Votre mot de passe">
                </div>

                <button type="submit" class="submit-button">
                    Se connecter
                </button>

                <p class="auth-switch">
                    Pas encore de compte ?
                    <a href="#" @click.prevent="goToRegister">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>

</template>