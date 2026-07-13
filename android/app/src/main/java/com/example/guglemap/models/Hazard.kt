package com.example.guglemap.models

import com.google.gson.annotations.SerializedName

data class Hazard(
    @SerializedName("id")
    val id: Int? = null,
    
    @SerializedName("user_name")
    val userName: String,
    
    @SerializedName("hazard_category")
    val hazardCategory: String,
    
    @SerializedName("hazard_description")
    val hazardDescription: String,
    
    @SerializedName("latitude")
    val latitude: Double,
    
    @SerializedName("longitude")
    val longitude: Double,
    
    @SerializedName("location_name")
    val locationName: String? = null,
    
    @SerializedName("device_info")
    val deviceInfo: String? = null,
    
    @SerializedName("reported_at")
    val reportedAt: String? = null
)
