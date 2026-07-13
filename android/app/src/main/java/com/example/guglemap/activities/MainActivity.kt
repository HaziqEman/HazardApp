package com.example.guglemap.activities

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import android.os.Bundle
import android.util.Log
import android.view.View
import android.widget.PopupMenu
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import androidx.core.app.ActivityCompat
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.lifecycle.ViewModelProvider
import com.example.guglemap.R
import com.example.guglemap.databinding.ActivityMainBinding
import com.example.guglemap.models.Hazard
import com.example.guglemap.network.RetrofitClient
import com.example.guglemap.repository.HazardRepository
import com.example.guglemap.viewmodel.HazardViewModel
import com.example.guglemap.viewmodel.HazardViewModelFactory
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import com.google.android.gms.location.Priority
import com.google.android.gms.maps.CameraUpdateFactory
import com.google.android.gms.maps.GoogleMap
import com.google.android.gms.maps.OnMapReadyCallback
import com.google.android.gms.maps.SupportMapFragment
import com.google.android.gms.maps.model.BitmapDescriptorFactory
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.MapStyleOptions
import com.google.android.gms.maps.model.MarkerOptions

class MainActivity : AppCompatActivity(), OnMapReadyCallback {

    private lateinit var binding: ActivityMainBinding
    private lateinit var mMap: GoogleMap
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private lateinit var viewModel: HazardViewModel
    private var lastLocation: Location? = null

    companion object {
        private const val LOCATION_PERMISSION_REQUEST_CODE = 1
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        // Load theme preference before super.onCreate
        val sharedPrefs = getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        val isDarkMode = sharedPrefs.getBoolean("is_dark_mode", false)
        if (isDarkMode) {
            AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)
        } else {
            AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)
        }

        val splashScreen = installSplashScreen()
        super.onCreate(savedInstanceState)
        
        // Keep splash screen visible for 2 seconds
        var keepSplash = true
        splashScreen.setKeepOnScreenCondition { keepSplash }
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        android.os.Handler(android.os.Looper.getMainLooper()).postDelayed({
            keepSplash = false
        }, 2000)

        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)

        val repository = HazardRepository(RetrofitClient.hazardApiService)
        val factory = HazardViewModelFactory(repository)
        viewModel = ViewModelProvider(this, factory)[HazardViewModel::class.java]

        val mapFragment = supportFragmentManager
            .findFragmentById(R.id.map) as SupportMapFragment
        mapFragment.getMapAsync(this)

        setupObservers()
        setupListeners()
    }

    private fun setupObservers() {
        viewModel.hazards.observe(this) { hazards ->
            displayHazards(hazards)
        }

        viewModel.loading.observe(this) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
        }

        viewModel.error.observe(this) { errorMessage ->
            errorMessage?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
            }
        }
    }

    private fun setupListeners() {
        binding.fabReport.setOnClickListener {
            val intent = Intent(this, ReportHazardActivity::class.java)
            lastLocation?.let {
                intent.putExtra("latitude", it.latitude)
                intent.putExtra("longitude", it.longitude)
            }
            startActivity(intent)
        }

        binding.btnRefresh.setOnClickListener {
            viewModel.fetchHazards()
        }

        binding.btnMenu.setOnClickListener { view ->
            showPopupMenu(view)
        }
    }

    private fun showPopupMenu(view: View) {
        val popup = PopupMenu(this, view)
        popup.menuInflater.inflate(R.menu.main_menu, popup.menu)

        // Update theme item icon/text based on current mode
        val themeItem = popup.menu.findItem(R.id.action_theme)
        val sharedPrefs = getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        val isDark = sharedPrefs.getBoolean("is_dark_mode", false)
        themeItem.title = if (isDark) getString(R.string.light_mode) else getString(R.string.dark_mode)
        themeItem.setIcon(if (isDark) android.R.drawable.ic_menu_day else android.R.drawable.ic_menu_month)

        popup.setOnMenuItemClickListener { item ->
            when (item.itemId) {
                R.id.action_theme -> {
                    toggleTheme()
                    true
                }
                R.id.action_about -> {
                    startActivity(Intent(this, AboutActivity::class.java))
                    true
                }
                else -> false
            }
        }
        popup.show()
    }

    private fun toggleTheme() {
        val sharedPrefs = getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        val isDarkMode = sharedPrefs.getBoolean("is_dark_mode", false)
        val newMode = !isDarkMode
        
        sharedPrefs.edit().putBoolean("is_dark_mode", newMode).apply()
        
        if (newMode) {
            AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)
        } else {
            AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)
        }
        recreate()
    }

    override fun onMapReady(googleMap: GoogleMap) {
        mMap = googleMap
        applyMapStyle()
        setUpMap()
        setupMarkerClickListener()
        
        // Display existing hazards if they're already loaded
        viewModel.hazards.value?.let { displayHazards(it) }
        
        viewModel.fetchHazards()
    }

    private fun applyMapStyle() {
        if (!::mMap.isInitialized) return
        val isDark = AppCompatDelegate.getDefaultNightMode() == AppCompatDelegate.MODE_NIGHT_YES
        if (isDark) {
            try {
                val success = mMap.setMapStyle(
                    MapStyleOptions.loadRawResourceStyle(this, R.raw.map_style)
                )
                if (!success) Log.e("MainActivity", "Style parsing failed.")
            } catch (e: Exception) {
                Log.e("MainActivity", "Can't find style. Error: ", e)
            }
        } else {
            mMap.setMapStyle(null)
        }
    }

    private fun setupMarkerClickListener() {
        mMap.setOnMarkerClickListener { marker ->
            val hazard = marker.tag as? Hazard
            if (hazard != null) {
                showHazardDetails(hazard)
                true
            } else {
                binding.cardHazardDetails.visibility = View.GONE
                false
            }
        }
        mMap.setOnMapClickListener {
            binding.cardHazardDetails.visibility = View.GONE
        }
    }

    private fun showHazardDetails(hazard: Hazard) {
        binding.cardHazardDetails.apply {
            visibility = View.VISIBLE
            
            binding.tvCategory.text = hazard.hazardCategory
            binding.tvDescription.text = hazard.hazardDescription
            binding.tvReporter.text = "Reported by: ${hazard.userName}"
            binding.tvLocation.text = "Lat: ${hazard.latitude}, Lon: ${hazard.longitude}"
            binding.tvDateTime.text = hazard.reportedAt ?: "Unknown date"

            // Set appropriate icon based on category
            val iconRes = when (hazard.hazardCategory) {
                "Building Hazard" -> android.R.drawable.ic_dialog_alert
                "Road Hazard" -> android.R.drawable.stat_sys_warning
                "Environmental Hazard" -> android.R.drawable.ic_menu_myplaces
                else -> android.R.drawable.ic_dialog_info
            }
            binding.ivHazardIcon.setImageResource(iconRes)

            binding.btnCloseCard.setOnClickListener {
                visibility = View.GONE
            }
        }
    }

    private fun setUpMap() {
        val permissions = arrayOf(
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION
        )

        if (permissions.any { ActivityCompat.checkSelfPermission(this, it) != PackageManager.PERMISSION_GRANTED }) {
            ActivityCompat.requestPermissions(this, permissions, LOCATION_PERMISSION_REQUEST_CODE)
            return
        }

        try {
            mMap.isMyLocationEnabled = true
            mMap.uiSettings.isMyLocationButtonEnabled = true

            if (!isGpsEnabled()) {
                Toast.makeText(this, "GPS is disabled. Please enable it for accurate location.", Toast.LENGTH_LONG).show()
            }

            fusedLocationClient.lastLocation.addOnSuccessListener(this) { location ->
                if (location != null) {
                    handleNewLocation(location)
                } else {
                    Log.d("MainActivity", "Last location is null, fetching current location...")
                    fetchCurrentLocation()
                }
            }
        } catch (e: SecurityException) {
            Log.e("MainActivity", "Security Exception: ${e.message}")
        }
    }

    private fun isGpsEnabled(): Boolean {
        val locationManager = getSystemService(Context.LOCATION_SERVICE) as LocationManager
        return locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) ||
                locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER)
    }

    private fun fetchCurrentLocation() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED &&
            ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            return
        }

        fusedLocationClient.getCurrentLocation(Priority.PRIORITY_HIGH_ACCURACY, null)
            .addOnSuccessListener { location ->
                if (location != null) {
                    handleNewLocation(location)
                } else {
                    Log.w("MainActivity", "Current location is null")
                    Toast.makeText(this, "Location not found. Ensure GPS is on.", Toast.LENGTH_SHORT).show()
                }
            }
            .addOnFailureListener { e ->
                Log.e("MainActivity", "Error fetching location: ${e.message}")
            }
    }

    private fun handleNewLocation(location: Location) {
        lastLocation = location
        Log.d("MainActivity", "Current GPS: ${location.latitude}, ${location.longitude}")
        val currentLatLng = LatLng(location.latitude, location.longitude)
        mMap.animateCamera(CameraUpdateFactory.newLatLngZoom(currentLatLng, 15f))
        
        // Refresh markers to include current location
        viewModel.hazards.value?.let { displayHazards(it) } ?: run {
            mMap.addMarker(MarkerOptions()
                .position(currentLatLng)
                .title("Current Location")
                .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE)))
        }
    }

    private fun displayHazards(hazards: List<Hazard>) {
        if (!::mMap.isInitialized) return
        mMap.clear()
        
        // Re-add current location marker if we have it
        lastLocation?.let {
            val currentLatLng = LatLng(it.latitude, it.longitude)
            mMap.addMarker(MarkerOptions()
                .position(currentLatLng)
                .title("Current Location")
                .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE)))
        }

        for (hazard in hazards) {
            val position = LatLng(hazard.latitude, hazard.longitude)
            val markerColor = when (hazard.hazardCategory) {
                "Building Hazard" -> BitmapDescriptorFactory.HUE_RED
                "Road Hazard" -> BitmapDescriptorFactory.HUE_YELLOW
                "Environmental Hazard" -> BitmapDescriptorFactory.HUE_GREEN
                else -> BitmapDescriptorFactory.HUE_CYAN
            }

            val marker = mMap.addMarker(MarkerOptions()
                .position(position)
                .title(hazard.hazardCategory)
                .icon(BitmapDescriptorFactory.defaultMarker(markerColor)))
            marker?.tag = hazard
        }
    }

    override fun onRequestPermissionsResult(requestCode: Int, permissions: Array<out String>, grantResults: IntArray) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == LOCATION_PERMISSION_REQUEST_CODE) {
            if (grantResults.isNotEmpty() && grantResults.any { it == PackageManager.PERMISSION_GRANTED }) {
                setUpMap()
            } else {
                Toast.makeText(this, "Location permission denied", Toast.LENGTH_SHORT).show()
            }
        }
    }
}
