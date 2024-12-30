import { ref, computed } from 'vue';

export function useSearch(products) {
  const searchQuery = ref('');

  const filteredProducts = computed(() => {
    const query = searchQuery.value.toLowerCase();
    if (!query) return products;
    
    return products.filter(product => 
      product.title.toLowerCase().includes(query) ||
      product.description.toLowerCase().includes(query)
    );
  });

  return {
    searchQuery,
    filteredProducts
  };
}