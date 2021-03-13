<template>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">My Blogs ()</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addNoteRoute'}">Add Note</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                    </div>
                    <div class="card-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    mounted() {
    }
}
</script>
