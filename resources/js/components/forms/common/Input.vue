<template>
  <div :class="['input-container', { flex }]">
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
      required: false
    },
    type: {
      type: String,
      default: 'text'
    },
    modelValue: {
      required: true
    },
    flex: {
      type: Boolean,
      default: false
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
    border: none;
    padding: 0.65em 0.85em;
    border-radius: 0.5em;
    // placeholder
    &::placeholder {
      font-style: italic;
    }
  }
  &.flex {
    display: flex;
    flex-direction: column;
    input {
      margin-top: 0.5em;
    }
  }
}
</style>