<template>
    <div class="vld-parent">
        <loading 
            color="#0a71b9"
            background-color="#000"
            :active.sync="isPageLoading"
            :can-cancel="false"
            :is-full-page="true"></loading>
    </div>
</template>
 
<script>
    // Import component
    import Loading from 'vue-loading-overlay';
    // Import stylesheet
    import 'vue-loading-overlay/dist/vue-loading.css';
    
    export default {
        props: [
            'is-Page-Loading'
        ],
        components: {
            Loading
        }
    }
</script> 