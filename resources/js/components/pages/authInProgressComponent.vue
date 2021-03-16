<template>
    <div>
        <div class="row">
            <div class="col-md-12 text-center">
                <i class="fas fa-cog fa-spin fa-3x"></i>
                <h1><strong>Authentication In-Progress...</strong></h1>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    methods: {
        goDashboard() {
            var _this = this;
            setTimeout( function () { 
                _this.$router.push({name: 'dashboardRoute'}).catch(()=>{});
            }, 5000);
        }
    },
    mounted() {
        var _this = this;
        _this.$root.isPageLoadingActive = true;
        _this.goDashboard();
    }
}
</script>
