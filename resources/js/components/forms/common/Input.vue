<template>
  <div class="input-container">
    <label v-if="label">{{ label }}</label>
    <input :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :placeholder="label" v-bind="$attrs"/>
  </div>
</template>

<script>
export default {
  name: 'Input',
  props: {
    label: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'text'
    },
    modelValue: {
      required: true
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped lang="scss">
.input-container {
  input {
    background-color: $inputBackgroundColour;
    outline: none;
    border: 1px solid $borderColour;
    padding: 0.5em 0.75em;
    border-radius: 0.5em;
  }
}
</style>