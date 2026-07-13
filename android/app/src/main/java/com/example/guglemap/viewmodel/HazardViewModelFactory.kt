package com.example.guglemap.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.example.guglemap.repository.HazardRepository

class HazardViewModelFactory(private val repository: HazardRepository) : ViewModelProvider.Factory {
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(HazardViewModel::class.java)) {
            @Suppress("UNCHECKED_CAST")
            return HazardViewModel(repository) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}
