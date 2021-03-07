<template>
    <div v-if="validationErrorListArr.length > 0" class="col-12 alert alert-danger fade show" role="alert" :data-count="validationErrorListArr.length">
        <div v-for="(error,index) in validationErrorListArr" :key="index">
            <li>{{error}}</li>
        </div>
    </div>
</template>

<script>
export default {
  props:{
    validationErrorListArr: {
      type: Array,
      default: []
    }
  }
}
</script>