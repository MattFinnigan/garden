<template>
  <div class="select-container">
    <label v-if="label">{{ label }}</label>
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
      required: false
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
  display: flex;
  align-items: center;
  select {
    background-color: $inputBackgroundColour;
    outline: none;
    border: none;
    padding: 0.65em 0.85em;
    flex: 1;
    border-radius: 0.5em;
    height: 45px;
  }
  label {
    min-width: 55px;
    display: inline-block;
  }
}
</style>