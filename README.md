# Bannerbear WordPress Plugin

A WordPress plugin that adds Bannerbear Signed URL functionality to WordPress sites.

- [About](#about)  
- [Installation](#installation)  
- [Usage](#usage) 
- [Examples](#examples)

## About

### What is Bannerbear?

[Bannerbear](https://www.bannerbear.com/) is a SaaS service that auto-generates images, based on dynamic parameters that you send, and templates you have set up on the Bannerbear back end.

### What does this plugin do?

This plugin provides an easy interface to add Bannerbear Signed URL images to your WP theme.

Bannerbear Signed URLs are dynamic urls that *generate images on the fly* based on templates. This plugin helps you map WP variables (title, date etc) to templates, and then inserts those URLs into WP posts and pages. 

One use case for this is auto-generating Open Graph images.

### Who is this plugin for?

This plugin requires a Bannerbear Scale or Enterprise account and is best suited for agencies managing WordPress sites for multiple clients, or individual users with large WordPress sites.

## Installation

Download the zip file from this repository or click [here](https://github.com/BannerbearHQ/wp-bannerbear/archive/refs/heads/main.zip) to download immediately.

Go to **Wordpress Plugins** and select **"Add New"**.

![](https://user-images.githubusercontent.com/49879122/202160760-ef50ebbe-1b9e-4a05-874f-a183fdefacdc.png)

Click **"Upload Plugin"** and upload the zip file.

![202097949-cc673d0e-6146-41b8-a953-97402d45fe21](https://user-images.githubusercontent.com/49879122/202165767-05e0552d-2201-48d7-b5ff-db6ea027b77b.png)

Install and activate the plugin.

![202098866-0c8783af-e7c1-4d78-a986-5bb81906c32f](https://user-images.githubusercontent.com/49879122/202165789-9028d5ae-44de-4685-bf4c-7da878036374.png) 

## Usage

### Creating a Signed URL Base

**Pre-requisite**: Create a Bannerbear project and add a template (or duplicate this [sample template](https://app.bannerbear.com/p/B2zYp0bOvEKD9J5mVZ)) to your project.

Go to **Bannerbear Plugin**.

![go to bb plugin](https://user-images.githubusercontent.com/49879122/202170742-cd04650c-5b9e-4d6c-bbe4-64d00a5a4e3f.png)

Select **”Add New”** and paste your Bannerbear Project [API Key](https://www.bannerbear.com/help/articles/64-where-do-i-get-my-api-key/).  

![api key](https://user-images.githubusercontent.com/49879122/202170565-381713de-713a-4d13-8bc6-0044618cd272.png)

Select from your list of templates.

![template list](https://user-images.githubusercontent.com/49879122/202170481-c504c6eb-82aa-40e9-85fa-ef7f76479753.png)

This will automatically create and grab a [Signed URL Base](https://www.bannerbear.com/help/articles/179-generate-images-using-signed-urls/) from the template, and all available template modifications will be listed.

### Template Configuration

Each API modifiable layer will have a dropdown menu that can be mapped with fields from the WordPress site. Select layers that you want to modify and choose where you want to apply the template/Signed URL.

![](https://user-images.githubusercontent.com/49879122/202156606-bcea9115-1671-4b1c-b22d-e6c78c9426ab.png)

It can also be embedded as a shortcode/snippet or added as a Block on WordPress pages.  

**Embed**

![](https://user-images.githubusercontent.com/49879122/202163755-011531db-5a08-42bc-937e-839aba0a93ec.png)

**Block** 

![](https://user-images.githubusercontent.com/49879122/202175799-6f66a67c-8940-4ff6-8e8c-a9c3cd05092b.png)

## Examples

When configured, this plugin will generate images on the fly based on your WordPress data. For example:

**Use as Open Graph Image** 

![](https://user-images.githubusercontent.com/49879122/202169570-6bd6f498-ec92-4ca3-84b8-587b62146792.png)

**Display on Posts/Pages (after Content)**

![](https://user-images.githubusercontent.com/49879122/202168459-4adc353f-386f-4454-b4bb-d7bafcdc003e.png)