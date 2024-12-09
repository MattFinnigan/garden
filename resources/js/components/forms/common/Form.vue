<template>
  <form>
    <div class="inputs-contain">
      <slot name="inputs"></slot>
    </div>
    <div class="buttons-contain">
      <slot name="buttons"></slot>
    </div>
  </form>
</template>

<script>
export default {
  name: 'Form',
  mounted () {
    // focus on first input
    this.$el.querySelector('input').focus()
  }
}
</script>

<style lang="scss" scoped>
form {

}
</style>