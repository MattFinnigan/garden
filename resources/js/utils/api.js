import axios from 'axios'

const axiosInstance = axios.create({
  withCredentials: true,
})

export const get = (url) => {
  return new Promise((resolve, reject) => {
    axiosInstance.get('/api/' + url).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}
export const post = (url, data) => {
  return new Promise((resolve, reject) => {
    axiosInstance.post('/api/' + url, data).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}
export const destroy = (url) => {
  return new Promise((resolve, reject) => {
    axiosInstance.delete('/api/' + url).then((response) => {
      resolve(response)
    }).catch((error) => {
      console.error(error)
      reject(error)
    })
  })
}

// plants
export const fetchPlants = (context, storeState = true) => {
  return new Promise((resolve) => {
    get('plants').then((response) => {
      console.log('fetchPlants', 'response', response)
      if (storeState) {
        context.$store.commit('plants/setPlants', response.data)
      }
      resolve(response)
    })
  })
}
export const fetchPlant = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    get(`plants/${id}`).then((response) => {
      console.log('fetchPlant', id, 'response', response)
      if (storeState) {
        context.$store.commit('plants/setCurrentPlant', response.data)
      }
      resolve(response)
    })
  })
}
export const createPlant = (context, data, storeState = true) => {
  return new Promise((resolve) => {
    post('plants', data).then((response) => {
      console.log('createPlant', data, 'response', response)
      if (storeState) {
        context.$store.commit('plants/setPlants', response.data.plants)
      }
      resolve(response)
    })
  })
}
export const updatePlant = (context, id, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`plants/${id}`, data).then((response) => {
      console.log('updatePlant', id, data, 'response', response)
      if (storeState) {
        context.$store.commit('plants/setPlants', response.data.plants)
      }
      resolve(response)
    })
  })
}
export const deletePlant = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    destroy(`plants/${id}`).then((response) => {
      console.log('deletePlant', id, 'response', response)
      if (storeState) {
        context.$store.commit('plants/setPlants', response.data.plants)
      }
      resolve(response)
    })
  })
}

// crops
export const fetchCrops = (context, month = null, storeState = true) => {
  return new Promise((resolve) => {
    const url = month ? `crops/month/${month}` : 'crops'
    get(url).then((response) => {
      console.log('fetchCrops', 'response', response)
      if (storeState) {
        context.$store.commit('crops/setCrops', response.data)
      }
      resolve(response)
    })
  })
}
export const fetchCrop = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    get(`crops/${id}`).then((response) => {
      console.log('fetchCrop', id, 'response', response)
      if (storeState) {
        context.$store.commit('crops/setCurrentCrop', response.data)
      }
      resolve(response)
    })
  })
}
export const createCrop = (context, data, month, storeState = true) => {
  return new Promise((resolve) => {
    post('crops', { ...data, month }).then((response) => {
      console.log('createCrop', data, 'response', response)
      if (storeState) {
        context.$store.commit('crops/setCrops', response.data.crops)
      }
      resolve(response)
    })
  })
}
export const updateCrop = (context, id, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`crops/${id}`, data).then((response) => {
      console.log('updateCrop', id, data, 'response', response)
      if (storeState) {
        const updatedList = context.$store.state.crops.list.map((crop) => {
          if (crop.id === id) {
            return { ...crop, ...response.data.crop }
          }
          return crop
        })
        context.$store.commit('crops/setCrops', updatedList)
      }
      resolve(response)
    })
  })
}
export const deleteCrop = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    destroy(`crops/${id}`).then((response) => {
      console.log('deleteCrop', id, 'response', response)
      if (storeState) {
        const updatedList = context.$store.state.crops.list.filter((crop) => crop.id !== id)
        context.$store.commit('crops/setCrops', updatedList)
        context.$store.commit('crops/setCurrentCrop', null)
      }
      resolve(response)
    })
  })
}

// crop entries
export const createCropEntry = (context, cropId, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`crop-entry/create/${cropId}`, data).then((response) => {
      if (response.data.status === 'success') {
        console.log('createCropEntry', cropId, data, 'response', response)
        if (storeState) {
          context.$store.commit('crops/setCrops', response.data.crops)
          context.$store.commit('crops/setCurrentCrop', response.data.crop)
        }
      } else {
        console.error('createCropEntry', 'response', response)
      }
      resolve(response)
    })
  })
}

export const updateCropEntry = (context, id, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`crop-entry/${id}`, data).then((response) => {
      if (response.data.status === 'success') {
        console.log('updateCropEntry', id, data, 'response', response)
        if (storeState) {
          context.$store.commit('crops/setCrops', response.data.crops)
          context.$store.commit('crops/setCurrentCrop', response.data.crop)
        }
      } else {
        console.error('updateCropEntry', 'response', response)
      }
      resolve(response)
    })
  })
}

export const deleteCropEntry = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    destroy(`crop-entry/${id}`).then((response) => {
      console.log('deleteCropEntry', id, 'response', response)
      if (storeState) {
        context.$store.commit('crops/setCrops', response.data.crops)
        context.$store.commit('crops/setCurrentCrop', response.data.crop)
      }
      resolve(response)
    })
  })
}

// beds
export const fetchBeds = (context, storeState = true) => {
  return new Promise((resolve) => {
    get('beds').then((response) => {
      console.log('fetchBeds', 'response', response)
      if (storeState) {
        context.$store.commit('beds/setBeds', response.data.beds)
      }
      resolve(response)
    })
  })
}

export const fetchBed = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    get(`beds/${id}`).then((response) => {
      console.log('fetchBed', id, 'response', response)
      if (storeState) {
        context.$store.commit('beds/setCurrentBed', response.data.bed)
      }
      resolve(response)
    })
  })
}
export const createBed = (context, data, storeState = true) => {
  return new Promise((resolve) => {
    post('beds', data).then((response) => {
      console.log('createBed', data, 'response', response)
      if (storeState) {
        context.$store.commit('beds/setCurrentBed', response.data.bed)
      }
      resolve(response)
    })
  })
}
export const updateBed = (context, id, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`beds/${id}`, data).then((response) => {
      console.log('updateBed', id, data, 'response', response)
      if (storeState) {
        const updatedList = context.$store.state.beds.list.map((bed) => {
          if (bed.id === id) {
            return { ...bed, ...response.data.bed }
          }
          return bed
        })
        context.$store.commit('beds/setBeds', updatedList)
      }
      resolve(response)
    })
  })
}
export const deleteBed = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    destroy(`beds/${id}`).then((response) => {
      console.log('deleteBed', id, 'response', response)
      if (storeState) {
        context.$store.commit('beds/setCurrentBed', null)
      }
      resolve(response)
    })
  })
}

// maps

export const fetchMaps = (context, date = null) => {
  return new Promise((resolve) => {
    get('maps?date=' + date).then((response) => {
      response.data = response.data.map((map) => {
        if (map.crops) {
          map.crops = map.crops.map((crop) => {
            crop = { ...crop, ...crop.crop }
            crop.latest_entry = crop
            return crop
          })
        }
        return map
      })
      console.log('fetchMaps', 'response', response)
      context.$store.commit('maps/setMaps', response.data)
      resolve(response)
    }).catch((error) => {
      console.error(error)
    })
  })
}

export const uploadImage = (data) => {
  return new Promise((resolve) => {
    const fd = new FormData()
    fd.append('image', data)
    post('image-upload', fd).then((response) => {
      console.log('uploadImage', 'response', response)
      resolve(response)
    })
  })
}