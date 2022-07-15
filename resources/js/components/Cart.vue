<template>
    <div class="dropdown">
        <!-- <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"> -->
        <a href="/checkout" class="dropdown-toggle">
            <i class="fa fa-shopping-cart"></i>
            <span>Your Cart</span>
            <div class="qty">{{itemCount}}</div>
        </a>
        <!-- <div class="cart-dropdown">
            <div class="cart-list">
                <div class="product-widget">
                    <div class="product-img">
                        <img src="assets/img/product01.png" alt="">
                    </div>
                    <div class="product-body">
                        <h3 class="product-name"><a href="#">product name goes here</a></h3>
                        <h4 class="product-price"><span class="qty">1x</span>$980.00</h4>
                    </div>
                    <button class="delete"><i class="fa fa-close"></i></button>
                </div>

                <div class="product-widget">
                    <div class="product-img">
                        <img src="assets/img/product02.png" alt="">
                    </div>
                    <div class="product-body">
                        <h3 class="product-name"><a href="#">product name goes here</a></h3>
                        <h4 class="product-price"><span class="qty">3x</span>$980.00</h4>
                    </div>
                    <button class="delete"><i class="fa fa-close"></i></button>
                </div>
            </div>
            <div class="cart-summary">
                <small>3 Item(s) selected</small>
                <h5>SUBTOTAL: $2940.00</h5>
            </div>
            <div class="cart-btns">
                <a href="#">View Cart</a>
                <a href="#">Checkout  <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div> -->
    </div>
</template>

<script>
    export default {
        data(){
            return {
                itemCount: ''
            }
        },
        mounted() {
            this.$root.$on('changeInCart', (item)=> {
                this.itemCount = item;
            })  
        },
        methods: {
            async getCartItemOnPageLoad(){
                let res = await axios.post('/cart');   
                console.log(res);   
                this.itemCount = res.data.items

            }
        },
        created(){
           this.getCartItemOnPageLoad();
        } 
    }
</script>
