const app = new Vue({
    el: '#app',
    data: {
        apiUrl: '../backend/api',
        products: [],
        categories: [],
        newProduct: {
            name: '',
            price: '',
            image: '',
            categories: []
        },
        newCategory: {
            name: ''
        },
        editingProduct: null,
        editingCategory: null,
        selectedFile: null
    },
    methods: {
        // Fetch data
        fetchProducts() {
            axios.get(`${this.apiUrl}/products.php`)
                .then(response => {
                    this.products = response.data;
                });
        },
        fetchCategories() {
            axios.get(`${this.apiUrl}/categories.php`)
                .then(response => {
                    this.categories = response.data;
                });
        },

        // Category methods
        saveCategory() {
            if (this.editingCategory) {
                axios.put(`${this.apiUrl}/categories.php?id=${this.editingCategory.id}`, this.newCategory)
                    .then(() => {
                        this.fetchCategories();
                        this.resetCategoryForm();
                    });
            } else {
                axios.post(`${this.apiUrl}/categories.php`, this.newCategory)
                    .then(() => {
                        this.fetchCategories();
                        this.resetCategoryForm();
                    });
            }
        },
        editCategory(category) {
            this.editingCategory = category;
            this.newCategory.name = category.name;
        },
        deleteCategory(id) {
            axios.delete(`${this.apiUrl}/categories.php?id=${id}`)
                .then(() => {
                    this.fetchCategories();
                });
        },
        resetCategoryForm() {
            this.newCategory.name = '';
            this.editingCategory = null;
        },

        // Product methods
        onFileChange(e) {
            this.selectedFile = e.target.files[0];
        },
        saveProduct() {
            if (this.selectedFile) {
                const formData = new FormData();
                formData.append('image', this.selectedFile);
                axios.post(`${this.apiUrl}/upload.php`, formData)
                    .then(response => {
                        this.newProduct.image = response.data.path;
                        this.finalizeSaveProduct();
                    });
            } else {
                this.finalizeSaveProduct();
            }
        },
        finalizeSaveProduct() {
            if (this.editingProduct) {
                axios.put(`${this.apiUrl}/products.php?id=${this.editingProduct.id}`, this.newProduct)
                    .then(() => {
                        this.fetchProducts();
                        this.resetProductForm();
                    });
            } else {
                axios.post(`${this.apiUrl}/products.php`, this.newProduct)
                    .then(() => {
                        this.fetchProducts();
                        this.resetProductForm();
                    });
            }
        },
        editProduct(product) {
            this.editingProduct = product;
            this.newProduct.name = product.name;
            this.newProduct.price = product.price;
            this.newProduct.image = product.image;
            if (product.category_ids) {
                this.newProduct.categories = product.category_ids.split(',').map(Number);
            } else {
                this.newProduct.categories = [];
            }
        },
        deleteProduct(id) {
            axios.delete(`${this.apiUrl}/products.php?id=${id}`)
                .then(() => {
                    this.fetchProducts();
                });
        },
        resetProductForm() {
            this.newProduct.name = '';
            this.newProduct.price = '';
            this.newProduct.image = '';
            this.newProduct.categories = [];
            this.editingProduct = null;
            this.selectedFile = null;
            this.$el.querySelector('input[type="file"]').value = null;
        }
    },
    mounted() {
        this.fetchProducts();
        this.fetchCategories();
    }
}); 