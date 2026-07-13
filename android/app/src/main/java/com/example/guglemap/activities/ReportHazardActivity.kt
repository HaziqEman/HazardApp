package com.example.guglemap.activities

import android.content.Context
import android.os.Build
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import androidx.lifecycle.ViewModelProvider
import com.example.guglemap.databinding.ActivityReportHazardBinding
import com.example.guglemap.models.Hazard
import com.example.guglemap.network.RetrofitClient
import com.example.guglemap.repository.HazardRepository
import com.example.guglemap.viewmodel.HazardViewModel
import com.example.guglemap.viewmodel.HazardViewModelFactory
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class ReportHazardActivity : AppCompatActivity() {

    private lateinit var binding: ActivityReportHazardBinding
    private lateinit var viewModel: HazardViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        val sharedPrefs = getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        val isDarkMode = sharedPrefs.getBoolean("is_dark_mode", false)
        AppCompatDelegate.setDefaultNightMode(
            if (isDarkMode) AppCompatDelegate.MODE_NIGHT_YES else AppCompatDelegate.MODE_NIGHT_NO
        )

        super.onCreate(savedInstanceState)
        binding = ActivityReportHazardBinding.inflate(layoutInflater)
        setContentView(binding.root)

        val repository = HazardRepository(RetrofitClient.hazardApiService)
        val factory = HazardViewModelFactory(repository)
        viewModel = ViewModelProvider(this, factory)[HazardViewModel::class.java]

        setupForm()
        setupObservers()

        binding.toolbar.setNavigationIcon(android.R.drawable.ic_menu_revert)
        binding.toolbar.setNavigationOnClickListener { finish() }
    }

    private fun setupForm() {
        // Categories for the dropdown
        val categories = arrayOf("Road Hazard", "Environmental Hazard", "Building Hazard")
        val adapter = ArrayAdapter(this, android.R.layout.simple_dropdown_item_1line, categories)
        binding.dropdownCategory.setAdapter(adapter)

        // Get location from intent
        val lat = intent.getDoubleExtra("latitude", 0.0)
        val lon = intent.getDoubleExtra("longitude", 0.0)
        binding.etLatitude.setText(lat.toString())
        binding.etLongitude.setText(lon.toString())

        // Set current date/time (Displayed for user reference)
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        binding.etDatetime.setText(sdf.format(Date()))

        binding.btnSubmit.setOnClickListener {
            submitReport()
        }
    }

    private fun setupObservers() {
        viewModel.loading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.btnSubmit.isEnabled = !isLoading
        }

        viewModel.reportSuccess.observe(this) { success ->
            if (success) {
                Toast.makeText(this, "Hazard reported successfully!", Toast.LENGTH_SHORT).show()
                finish()
            }
        }

        viewModel.error.observe(this) { errorMessage ->
            errorMessage?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun submitReport() {
        val username = binding.etUsername.text.toString().trim()
        val category = binding.dropdownCategory.text.toString()
        val description = binding.etDescription.text.toString().trim()
        val lat = binding.etLatitude.text.toString().toDoubleOrNull() ?: 0.0
        val lon = binding.etLongitude.text.toString().toDoubleOrNull() ?: 0.0
        
        // Validation
        if (username.isEmpty() || category.isEmpty() || description.isEmpty()) {
            Toast.makeText(this, "Please fill in all fields", Toast.LENGTH_SHORT).show()
            return
        }

        // Get current timestamp for Laravel
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        val currentDateTime = sdf.format(Date())

        // Create Hazard object matching Laravel schema
        val hazard = Hazard(
            userName = username,
            hazardCategory = category,
            hazardDescription = description,
            latitude = lat,
            longitude = lon,
            deviceInfo = "${Build.MANUFACTURER} ${Build.MODEL}",
            locationName = "GPS Coordinates",
            reportedAt = currentDateTime
        )

        viewModel.reportHazard(hazard)
    }
}
