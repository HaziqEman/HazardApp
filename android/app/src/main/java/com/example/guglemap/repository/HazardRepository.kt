package com.example.guglemap.repository

import com.example.guglemap.models.Hazard
import com.example.guglemap.network.HazardApiService
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import retrofit2.Response

class HazardRepository(private val apiService: HazardApiService) {

    suspend fun getHazards(): Response<List<Hazard>> {
        return withContext(Dispatchers.IO) {
            apiService.getHazards()
        }
    }

    suspend fun getHazard(id: Int): Response<Hazard> {
        return withContext(Dispatchers.IO) {
            apiService.getHazard(id)
        }
    }

    suspend fun reportHazard(hazard: Hazard): Response<Hazard> {
        return withContext(Dispatchers.IO) {
            apiService.reportHazard(hazard)
        }
    }

    suspend fun deleteHazard(id: Int): Response<Unit> {
        return withContext(Dispatchers.IO) {
            apiService.deleteHazard(id)
        }
    }
}
