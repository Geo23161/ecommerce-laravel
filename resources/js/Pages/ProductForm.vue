<script setup>
import { ref, onMounted } from 'vue'
import { TEMP_PROD } from '../utils/constants';
import GlobalLoader from './components/GlobalLoader.vue';
import { router as Inertia } from '@inertiajs/vue3';
import { openUploadModal } from "@bytescale/upload-widget-vue";
import { isTokenInLocalStorage, validateOrRefreshToken } from "../utils";

onMounted(() => {
    const prod = localStorage.getItem(TEMP_PROD);
    if (prod != null) {
        product.value = JSON.parse(prod);
        imagePreview.value = product.value.image;
    }
});

const product = ref({
    title: '',
    price: 0,
    discount: 0,
    description: '',
    image: null
})

const options = {
    apiKey: "public_kW15cLo5dNcEiabWWRqkaiPYjNXA", // This is your API key.
    maxFileCount: 1
};

async function uploadFile(event) {
    openUploadModal({
        event,
        options,
        onComplete: (files) => {
            if (files.length === 0) {
                alert("Aucun fichier selectionnés");
            } else {
                imagePreview.value = files[0].fileUrl;
            }
        }
    })
}

const imagePreview = ref(null)

const handleImageUpload = (event) => {
    const input = event.target
    if (input.files && input.files[0]) {
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target?.result
            product.value.image = imagePreview.value
        }
        reader.readAsDataURL(input.files[0])
    }
}

const createProduct = async (productData) => {
    if(productData['image'] == null) {
        alert("Veuillez choisir une image.");
        return
    }

    try {
        // Make a POST request to the API to create the product
        const response = await axios.post('/api/produits', productData, {
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${await validateOrRefreshToken()}`, // Include authentication token
            },
        });

        console.log('Produit créé avec succès:', response.data);

        // Optionally add the newly created product to your local state
        Inertia.visit('/vendor');
    } catch (error) {
        alert('Erreur lors de la création du produit.');
        console.log(error);
    }
};


const updateProduct = async (productId, updatedData) => {
    try {
        // Effectuer une requête PUT ou PATCH pour mettre à jour le produit
        const response = await axios.put(`/api/produits/${productId}`, updatedData, {
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${await validateOrRefreshToken()}`, // Inclure le token JWT pour l'authentification
            },
        });

        console.log('Produit mis à jour avec succès:', response.data);
        Inertia.visit('/vendor');

    } catch (error) {
        // Gérer les erreurs
        alert('Erreur lors de la mise à jour du produit.');
        console.log(error);
    }
};

const isLoading = ref(false);
const handleSubmit = async () => {
    isLoading.value = true;
    const data = {
        ...product.value
    };
    if (product.value['image'] != imagePreview.value) {
        data['image'] = imagePreview.value
    }
    data['quantite'] = 1
    if (product.value['id'] != null) {
        await updateProduct(product.value['id'], data);
    } else {
        await createProduct(data);
    }
    isLoading.value = false;
}
</script>

<template>
    <div>

        <GlobalLoader :is-loading="isLoading" />

        <div class="product-form">
            <div class="image-upload">
                <div class="image-preview" :class="{ 'has-image': imagePreview }">
                    <img v-if="imagePreview" :src="imagePreview" alt="Product preview" />
                    <div v-else class="upload-placeholder">
                        <span>Appuyez pour ajouter une image</span>
                    </div>
                </div>

                <div @click="uploadFile" class="file-input">

                </div>
                <!-- <input
        type="file"
        accept="image/*"
        @change="handleImageUpload"
        class="file-input"
      /> -->
            </div>

            <form @submit.prevent="handleSubmit" class="form-content">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input id="name" v-model="product.title" type="text" required
                        placeholder="Entrez le nom du produit" />
                </div>

                <div class="form-group">
                    <label for="price">Prix</label>
                    <input id="price" v-model="product.price" type="number" required min="0" step="0.01"
                        placeholder="Entrez le prix" />
                </div>

                <div class="form-group">
                    <label for="discount">Reduction (%)</label>
                    <input id="discount" v-model="product.discount" type="number" min="0" max="100"
                        placeholder="Entrez le pourcentage" />
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" v-model="product.description" rows="4"
                        placeholder="Description du produit"></textarea>
                </div>

                <button type="submit" class="submit-button">Save Product</button>
            </form>
        </div>
    </div>

</template>

<style scoped>
.product-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
}

.image-upload {
    margin-bottom: 2rem;
    position: relative;
}

.image-preview {
    width: 100%;
    height: 300px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #f9f9f9;
}

.image-preview.has-image {
    border-style: solid;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-placeholder {
    color: #666;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.form-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

label {
    font-weight: 600;
    color: #333;
}

input,
textarea {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

textarea {
    resize: vertical;
}

.submit-button {
    background-color: #2563eb;
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.submit-button:hover {
    background-color: #5e89e6;
}
</style>