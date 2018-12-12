<template>
    <div id="modal" :class="{ show : modalOpen }">
        <button @click="modalOpen = false" class="modal-close">&times;</button>
        <div class="modal-content-img">
            <slot></slot>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                modalOpen: false
            }
        },
        methods: {
            escapeKeyListener(evt) {
                if (evt.keyCode === 27 && this.modalOpen) {
                    this.modalOpen = false;
                }
            }
        },
        watch: {
            modalOpen() {
                var className = ['modal-open', 'v-modal'];
                if (this.modalOpen) {
                    document.body.classList.add(...className);
                } else {
                    document.body.classList.remove(...className);
                    Fire.$emit('vModalClose');
                }
            }
        },
        created() {
            document.addEventListener('keyup', this.escapeKeyListener);
        },
        destroyed() {
            document.removeEventListener('keyup', this.escapeKeyListener);
        },
    }
</script>
<style>
    .v-modal{
        height: 100vh !important;
        overflow: hidden;
    }
    #modal {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1101;
        background-color: rgba(0,0,0,0.85);
    }

    #modal.show {
        display: block;
    }

    #modal .modal-content-img {
        height: 100%;
        max-width: 105vh;
        padding-top: 12vh;
        margin: 0 auto;
        position: relative;
        text-align: center;
    }

    #modal .modal-content-img, #modal #caption, #modal .modal-close {    
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {-webkit-transform:scale(0)} 
        to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    .modal-close {
        cursor: pointer;
        position: absolute;
        right: 0;
        top: 0;
        padding: 0px 28px 8px;
        font-size: 3em;
        width: auto;
        height: auto;
        background: transparent;
        border: 0;
        outline: none;
        color: #ffffff;
        z-index: 1000;
        font-weight: 100;
        line-height: 1;
    }
</style>
