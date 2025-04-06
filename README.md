Rainscript Slider - WordPress Plugin

Create beautiful, center-focused sliders using selected pages or post categories.

Rainscript Slider Preview (Replace with your actual preview image)
Features

    🎨 Create sliders from selected pages or post categories

    🖱️ Center-focused design with smooth transitions

    ⚙️ Easy-to-use admin interface

    📱 Fully responsive for all devices

    🚀 Fast loading with Slick Carousel

    🔗 Built-in "Read More" buttons for each slide

Installation

    Download the plugin ZIP file

    In your WordPress admin, go to Plugins > Add New > Upload Plugin

    Upload the ZIP file and click Install Now

    Activate the plugin

Usage

    After activation, go to Rainscript Sliders in your WordPress admin

    Create a new slider and select pages or categories to include

    Copy the generated shortcode (e.g., [viva_page_slider id="your-slider-id"])

    Paste the shortcode into any page, post, or widget area

Shortcode Options
Attribute	Description	Default Value
id	The slider ID (required)	-
button_text	Text for the "Read More" button	"Read More"
button_style	Button style (default/primary/ghost)	"default"

Example:
[viva_page_slider id="homepage-slider" button_text="Learn More" button_style="primary"]
Customization

Add this CSS to your theme's Additional CSS section (Appearance > Customize) to override styles:
css
Copy

/* Custom slider styles */
.viva-page-slider {
    margin: 2rem 0;
}

/* Custom button styles */
.viva-read-more-btn {
    background: #your-color;
    color: #your-color;
}

/* Center mode adjustments */
.slick-center .viva-slide {
    transform: scale(1.05);
}

Changelog
1.6 (Current)

    Added multi-category selection support

    Improved mobile responsiveness

    Enhanced "Read More" button styling

    Fixed slider initialization issues

1.5

    Initial public release

    Basic slider functionality
