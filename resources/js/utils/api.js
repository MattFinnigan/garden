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
export const fetchCrops = (context, storeState = true) => {
  return new Promise((resolve) => {
    get('crops').then((response) => {
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
export const createCrop = (context, data, storeState = true) => {
  return new Promise((resolve) => {
    post('crops', data).then((response) => {
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
        context.$store.commit('crops/setCrops', response.data.crops)
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
        context.$store.commit('crops/setCrops', response.data.crops)
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

// locations
export const fetchLocations = (context, storeState = true) => {
  return new Promise((resolve) => {
    get('locations').then((response) => {
      console.log('fetchLocations', 'response', response)
      if (storeState) {
        context.$store.commit('locations/setLocations', response.data)
      }
      resolve(response)
    })
  })
}
export const fetchLocation = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    get(`locations/${id}`).then((response) => {
      console.log('fetchLocation', id, 'response', response)
      if (storeState) {
        context.$store.commit('locations/setCurrentLocation', response.data)
      }
      resolve(response)
    })
  })
}
export const createLocation = (context, data, storeState = true) => {
  return new Promise((resolve) => {
    post('locations', data).then((response) => {
      console.log('createLocation', data, 'response', response)
      if (storeState) {
        context.$store.commit('locations/setLocations', response.data.locations)
      }
      resolve(response)
    })
  })
}
export const updateLocation = (context, id, data, storeState = true) => {
  return new Promise((resolve) => {
    post(`locations/${id}`, data).then((response) => {
      console.log('updateLocation', id, data, 'response', response)
      if (storeState) {
        context.$store.commit('locations/setLocations', response.data.locations)
      }
      resolve(response)
    })
  })
}
export const deleteLocation = (context, id, storeState = true) => {
  return new Promise((resolve) => {
    destroy(`locations/${id}`).then((response) => {
      console.log('deleteLocation', id, 'response', response)
      if (storeState) {
        context.$store.commit('locations/setLocations', response.data.locations)
      }
      resolve(response)
    })
  })
}

// beds
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
        context.$store.commit('beds/setCurrentBed', response.data.bed)
      }
      resolve(response)
    })
  })
}

// maps

export const fetchMaps = (context, date = null) => {
  return new Promise((resolve) => {
    get('maps?date=' + date).then((response) => {
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