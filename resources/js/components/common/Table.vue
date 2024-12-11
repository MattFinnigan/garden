<template>
  <div class="table-component">
    <div v-if="header" class="table-header">
      <slot name="header"></slot>
    </div>
    <div v-if="prefix" class="table-prefix">
      <slot name="prefix"></slot>
    </div>
    <table v-if="rows.length">
      <thead>
        <tr>
          <th v-for="header in headers" :key="header" :style="header.width ? 'width:' + header.width : ''">{{ header.label }}</th>
          <th v-if="actions" style="width:110px"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, i) in rows" :key="row.id || i">
          <td v-for="header in headers" :key="header.key">
            <span v-html="header.key === 'num' ? i + 1 : row[header.key]"></span>
          </td>
          <td v-if="actions">
            <Button v-if="actions.view" classes="sm" @click="$emit('view', row, i)" view></Button>
            <Button v-if="actions.edit" classes="sm" @click="$emit('edit', row, i)" edit></Button>
            <Button v-if="actions.delete" classes="sm" @click="$emit('delete', row, i)" del></Button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="rows.length" class="mobile-table">
      <div v-for="(row, i) in rows" :key="row.id || i" class="row">
        <div v-for="header in headers" :key="header.key" class="header">
          <div class="row-data">
            <span v-html="header.key === 'num' ? ('#' + i + 1) : row[header.key]"></span>
          </div>
        </div>
        <div v-if="actions" class="action-buttons">
          <Button v-if="actions.view" classes="sm" @click="$emit('view', row, i)">View</Button>
          <Button v-if="actions.edit" classes="sm" @click="$emit('edit', row, i)">Edit</Button>
          <Button v-if="actions.delete" classes="sm" @click="$emit('delete', row, i)">Delete</Button>
        </div>
      </div>
    </div>
    <div v-if="!rows.length && empty"><slot name="empty"></slot></div>
  </div>
</template>

<script>
export default {
  name: 'TableComponent',
  props: {
    headers: Array,
    rows: Array,
    actions: Object
  },
  computed: {
    header() {
      return !!this.$slots.header
    },
    prefix() {
      return !!this.$slots.prefix
    },
    empty() {
      return !!this.$slots.empty
    }
  }
}
</script>

<style scoped lang="scss">
.table-component {
  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  table {
    display: none;
    width: 100%;
    border-collapse: collapse;
    border: 1px solid $grey-500;
    border-radius: 0.5em;
    thead {
      background-color: $grey-700;
      border-bottom: 1px solid $grey-500;
      th {
        padding: 10px;
        text-align: left;
        font-size: $fsSmall;
      }
    }
    tbody {
      tr {
        &:nth-child(even) {
          background-color: $grey-800;
        }
        td {
          padding: 10px;
          font-size: $fsSmall;
        }
      }
    }
  }
  .mobile-table {
    display: block;
    .row {
      padding: 10px;
      border-bottom: 1px solid $grey-500;
      .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 2px 0;
        .row-data {
          flex: 1;
          font-size: $fsSmall;
        }
      }
    }
    > :nth-child(even) {
      background-color: $grey-800;
    }
  }
  @include device (desktop, 'all') {
    table {
      display: table;
    }
    .mobile-table {
      display: none;
    }
  }
}
</style>