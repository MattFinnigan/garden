<template>
  <div class="landing">
    Landing!
  </div>
</template>

<script>
export default {
  name: 'Landing',
  mounted () {
    console.log(this)
    this.$axios.get('/api/plants').then(response => {
      console.log(response.data)
    })
  }
}
</script>

<style scoped lang="scss">

</style>