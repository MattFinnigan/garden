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
  .inputs-contain {
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
    :deep(input), :deep(select) {
      min-width: 350px;
      height: 35px;
      padding-left: 0.5em;
      background: $grey-700;
      color: $white;
      outline: none;
      border: 1px solid $grey-500;
      border-radius: 0.25em;
    }
    :deep(.display-input) {
      display: flex;
      flex-direction: column;
      margin-bottom: 0.75rem;
      label {
        margin-bottom: 0.25rem;
      }
    }
    :deep(.inputs-row) {
      display: flex;
      > * {
        flex: 1;
        margin-right: 0.5rem;
      }
    }
  }
}
</style>