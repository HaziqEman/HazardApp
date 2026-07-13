package com.example.guglemap.activities

import android.content.Context
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import com.example.guglemap.databinding.ActivityAboutBinding

class AboutActivity : AppCompatActivity() {

    private lateinit var binding: ActivityAboutBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        val sharedPrefs = getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        val isDarkMode = sharedPrefs.getBoolean("is_dark_mode", false)
        AppCompatDelegate.setDefaultNightMode(
            if (isDarkMode) AppCompatDelegate.MODE_NIGHT_YES else AppCompatDelegate.MODE_NIGHT_NO
        )

        super.onCreate(savedInstanceState)
        binding = ActivityAboutBinding.inflate(layoutInflater)
        setContentView(binding.root)
        
        binding.toolbar.setNavigationIcon(android.R.drawable.ic_menu_revert)
        binding.toolbar.setNavigationOnClickListener { finish() }
    }

    override fun onSupportNavigateUp(): Boolean {
        finish()
        return true
    }
}
