import { ref } from 'vue';
import { validateOrRefreshToken } from "../../utils";


export function useOrderHistory() {
  const orders = ref([]);
  const ordLoading = ref(false)

  const addOrder = async (items, total) => {
    ordLoading.value = true;
    await createOrder(items, total);
    ordLoading.value = false;
  };

  const fetchOrders = async () => {
    try {
      // Effectuer la requête pour récupérer les commandes
      const response = await axios.get('/api/orders', {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });
  
      // Stocker les données des commandes dans 'orders'
      orders.value = response.data;
      console.log("THis is orders");
      console.log(orders.value);
  
    } catch (error) {
      // Gérer les erreurs
      console.error('Erreur lors de la récupération des commandes.', error);
    }
  };

  const createOrder = async (items, total) => {
    try {
      // Assurez-vous que le panier n'est pas vide
      if (items.length === 0) {
        alert('Le panier est vide, impossible de passer la commande.');
        return;
      }
  
      
      const response = await axios.post('/api/orders', {
        cart_items: JSON.stringify(items), 
        total: total
      }, {
        headers : {
                  'Content-Type': 'application/json',
                  Authorization: `Bearer ${await validateOrRefreshToken()}`
              }
      });
  
      
      console.log('Commande cre avec succès:', response.data);
      
      
      orders.value = [];
      await fetchOrders();

    } catch (error) {
      
      console.error('Erreur lors de la création de la commande.', error);
    }
  };
  

  fetchOrders();

  return {
    orders,
    addOrder,
    ordLoading
  };
}