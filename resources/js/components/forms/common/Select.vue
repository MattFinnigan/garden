<template>
  <div class="select-container">
    <label>{{ label }}</label>
    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" v-bind="$attrs">
      <option v-for="option in options" :value="option.value">{{ option.label }}</option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'Select',
  props: {
    label: {
      type: String,
      required: true
    },
    options: {
      type: Array,
      required: true
    },
    modelValue: {
      required: true
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped lang="scss">
.select-container {
  select {
    background-color: $inputBackgroundColour;
    outline: none;
    border: 1px solid $borderColour;
    padding: 0.5em 0.75em;
    border-radius: 0.5em;
  }
}
</style>