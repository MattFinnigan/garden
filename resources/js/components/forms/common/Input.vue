<template>
  <div :class="['input-container', { flex }]">
    <label v-if="label">{{ label }}</label>
    <input :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :placeholder="label" v-bind="$attrs"/>
    <div v-if="suffix" v-html="suffix"></div>
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
    },
    suffix: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped lang="scss">
.input-container {
  display: flex;
  align-items: center;
  input {
    background-color: $inputBackgroundColour;
    outline: none;
    border: none;
    padding: 0.65em 0.85em;
    border-radius: 0.5em;
    flex: 1;
    // placeholder
    &::placeholder {
      font-style: italic;
    }
  }
  &.flex {
    flex-direction: column;
    align-items: unset;
    input {
      margin-top: 0.5em;
    }
  }
  label {
    min-width: 60px;
    display: inline-block;
  }
}
</style>