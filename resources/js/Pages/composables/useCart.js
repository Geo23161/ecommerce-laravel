import { ref, computed } from 'vue';
import { validateOrRefreshToken } from "../../utils";


export function useCart() {
  const items = ref([]);
  const isCartOpen = ref(false);
  const errorMessage = ref("");
  const successMessage = ref("");
  const cartLoading = ref(false);

  const addToCart = async (product) => {
    const existingItem = items.value.find(item => item.product.id === product.id);
    if (existingItem) {
      await updateCartItem(existingItem.id, existingItem.quantity);
      
      await initCart();
      


    } else {
      await addToCartApi(product);
       await initCart();
      
      // items.value.push({ product, quantity: 1 });
      
    }
    isCartOpen.value = true;
  };

  const initCart = async () => {
    items.value = [];
      await fetchCartItems();
  }

  const removeFromCart = async (productId) => {
    const existingItem = items.value.find(item => item.product.id === productId);
    if (existingItem != null) {
      console.log(existingItem);
      await removeItemCart(existingItem.id);
      
      await initCart();
      
    }

  };

  const updateQuantity = async (productId, quantity) => {
    const item = items.value.find(item => item.product.id === productId);
    if (item) {
      if (quantity <= 0) {
        await removeFromCart(productId);
      } else {
        await updateCartItem(item.id, quantity);
        
          item.quantity = quantity;
        

      }
      
    }
  };

  const totalItems = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantity, 0)
  );

  const totalPrice = computed(() =>
    items.value.reduce((sum, item) => {
      const price = item.product.discount
        ? item.product.price * (1 - item.product.discount / 100)
        : item.product.price;
      return sum + price * item.quantity;
    }, 0)
  );

  const addToCartApi = async (product) => {
    cartLoading.value = true;
    try {
      const response = await axios.post('/api/cart/add', {
        product_id: product['id'],
        quantity: 1,
      }, {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });

      successMessage.value = 'Produit ajouté au panier avec succès!';

    } catch (error) {
      alert('Erreur lors de l\'ajout du produit au panier.');
      console.error(error);
    } 
      cartLoading.value = false;
    

  };

  const updateCartItem = async (id, quantity) => {
    cartLoading.value = true;
    errorMessage.value = null;

    try {
      const response = await axios.patch(`/api/cart/update/${id}`, {
        quantity,
      }, {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });


      successMessage.value = 'Quantité mise à jour avec succès!';
    } catch (error) {
      alert('Erreur lors de la mise à jour du panier.');
      console.error(error);
    } 
      
    cartLoading.value = false;
  };



  const fetchCartItems = async () => {
    console.log("Headers => ");
    console.log({
        'Content-Type': 'application/json',
        Authorization: `Bearer ${await validateOrRefreshToken()}`
    });
    try {
      const response = await axios.get('/api/cart', {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });
      items.value = response.data;
      console.log(items.value);
    } catch (error) {
      console.error('Erreur lors de la récupération des éléments du panier.');
      console.error(error);
    }
  };

  const removeItemCart = async (itemId) => {
    cartLoading.value = true;
    errorMessage.value = null;

    try {
      const response = await axios.delete(`/api/cart/remove/${itemId}`, {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });
      // items.value = response.data.cart;
      successMessage.value = 'Produit supprimé du panier avec succès!';
    } catch (error) {
      alert('Erreur lors de la suppression du produit du panier.');
      console.error(error);
    } finally {
      cartLoading.value = false;
    }
  };


  fetchCartItems();


  return {
    items,
    isCartOpen,
    addToCart,
    removeFromCart,
    updateQuantity,
    totalItems,
    totalPrice,
    successMessage,
    errorMessage,
    cartLoading
  };
}