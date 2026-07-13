package com.example.guglemap.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.guglemap.models.Hazard
import com.example.guglemap.repository.HazardRepository
import kotlinx.coroutines.launch

class HazardViewModel(private val repository: HazardRepository) : ViewModel() {

    private val _hazards = MutableLiveData<List<Hazard>>()
    val hazards: LiveData<List<Hazard>> get() = _hazards

    private val _singleHazard = MutableLiveData<Hazard?>()
    val singleHazard: LiveData<Hazard?> get() = _singleHazard

    private val _loading = MutableLiveData<Boolean>()
    val loading: LiveData<Boolean> get() = _loading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> get() = _error

    private val _reportSuccess = MutableLiveData<Boolean>()
    val reportSuccess: LiveData<Boolean> get() = _reportSuccess

    private val _deleteSuccess = MutableLiveData<Boolean>()
    val deleteSuccess: LiveData<Boolean> get() = _deleteSuccess

    fun fetchHazards() {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val response = repository.getHazards()
                if (response.isSuccessful) {
                    _hazards.value = response.body() ?: emptyList()
                } else {
                    _error.value = "Server Error: ${response.code()}"
                }
            } catch (e: Exception) {
                _error.value = "Connection Failed: ${e.localizedMessage}"
            } finally {
                _loading.value = false
            }
        }
    }

    fun fetchHazard(id: Int) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            try {
                val response = repository.getHazard(id)
                if (response.isSuccessful) {
                    _singleHazard.value = response.body()
                } else {
                    _error.value = "Could not find hazard"
                }
            } catch (e: Exception) {
                _error.value = "Error: ${e.localizedMessage}"
            } finally {
                _loading.value = false
            }
        }
    }

    fun reportHazard(hazard: Hazard) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            _reportSuccess.value = false
            try {
                val response = repository.reportHazard(hazard)
                if (response.isSuccessful) {
                    _reportSuccess.value = true
                    fetchHazards() // Refresh the list
                } else {
                    val errorBody = response.errorBody()?.string()
                    _error.value = "Failed to report (${response.code()}): $errorBody"
                }
            } catch (e: Exception) {
                _error.value = "Submission Failed: ${e.localizedMessage}"
            } finally {
                _loading.value = false
            }
        }
    }

    fun deleteHazard(id: Int) {
        viewModelScope.launch {
            _loading.value = true
            _error.value = null
            _deleteSuccess.value = false
            try {
                val response = repository.deleteHazard(id)
                if (response.isSuccessful) {
                    _deleteSuccess.value = true
                    fetchHazards() // Refresh list after deletion
                } else {
                    _error.value = "Failed to delete"
                }
            } catch (e: Exception) {
                _error.value = "Delete failed: ${e.localizedMessage}"
            } finally {
                _loading.value = false
            }
        }
    }
}
