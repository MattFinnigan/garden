<template>
  <form>
    <div class="form-inputs-contain">
      <slot name="inputs"></slot>
    </div>
    <div class="form-buttons-contain">
      <slot name="buttons"></slot>
    </div>
  </form>
</template>

<script>
export default {
  name: 'Form',
  mounted () {
    this.$nextTick(() => {
      // focus on first input
      this.$el.querySelector('input')?.focus()
    })
  }
}
</script>

<style lang="scss" scoped>
form {
  :deep(.error) {
    color: #ff0000;
  }
  .form-inputs-contain > * {
    margin-bottom: 1em;
  }
  .form-buttons-contain {
    display: flex;
    justify-content: flex-end;
    margin-top: 1em;
    > * {
      margin-left: 1em;
    }
  }
}
</style>