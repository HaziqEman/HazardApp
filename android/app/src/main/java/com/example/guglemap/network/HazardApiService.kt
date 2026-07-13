package com.example.guglemap.network

import com.example.guglemap.models.Hazard
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path

interface HazardApiService {

    @GET("hazards")
    suspend fun getHazards(): Response<List<Hazard>>

    @GET("hazards/{id}")
    suspend fun getHazard(@Path("id") id: Int): Response<Hazard>

    @POST("hazards")
    suspend fun reportHazard(@Body hazard: Hazard): Response<Hazard>

    @DELETE("hazards/{id}")
    suspend fun deleteHazard(@Path("id") id: Int): Response<Unit>

    companion object {
        // Use 10.0.2.2 to access localhost from the Android Emulator
        const val BASE_URL = "http://10.0.2.2:8000/api/"
    }
}
