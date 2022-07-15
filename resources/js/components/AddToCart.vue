<template>
    <div class="add-to-cart">
        <button class="add-to-cart-btn" @click.prevent="addProductToCart()"><i class="fa fa-shopping-cart"></i> add to cart</button>
    </div>
</template>

<script>
import axios from 'axios';

    export default {
        data(){
            return {
                
            }
        },
        props: ['productId','userId'],
        methods: {
           async addProductToCart(){
                // alert(this.productId);

                // Check if user is logged in or not
                if(this.userId == 0){
                    this.$toastr.e("You need to login, to add this product in Cart");
                    return;
                }

                //If user is logged in then add item to Cart
                let response = await axios.post('/cart',{
                    'product_id': this.productId
                });
                
                this.$root.$emit('changeInCart', response.data.items);
            }
        },
        mounted() {
        }
    }
</script>
