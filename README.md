# Bannerbear WordPress Plugin

A WordPress plugin to add the Bannerbear Signed URL functionality to WordPress sites easily. It can be used to add dynamically generated images to posts and pages, or generate custom Open Graph images automatically.  
- [Documentation](#documentation)  
- [Installation](#installation)  
- [Usage](#usage) 
  - [Configuration](#configuration)
  - [Examples](#examples)


## Documentation

Find the full Signed URL documentation [here](https://github.com/yongfook/bannerbear-signed-url-examples#on-demand-signed-urls).

## **Installation**

Download the zip file from this repository or click [here](https://github.com/BannerbearHQ/wp-bannerbear/archive/refs/heads/main.zip) to download immediately.

Go to **Wordpress Plugins** and select **"Add New"**.
<img width="1437" alt="Screenshot 2022-11-16 at 6 48 40 PM" src="https://user-images.githubusercontent.com/49879122/202160760-ef50ebbe-1b9e-4a05-874f-a183fdefacdc.png">



Click **"Upload Plugin"** and upload the zip file.
![202097949-cc673d0e-6146-41b8-a953-97402d45fe21](https://user-images.githubusercontent.com/49879122/202165767-05e0552d-2201-48d7-b5ff-db6ea027b77b.png)


Install and activate the plugin.

![202098866-0c8783af-e7c1-4d78-a986-5bb81906c32f](https://user-images.githubusercontent.com/49879122/202165789-9028d5ae-44de-4685-bf4c-7da878036374.png) 

## Usage

**Pre-requisite**: Create a Bannerbear project and add a template (or duplicate this [sample template](https://app.bannerbear.com/p/B2zYp0bOvEKD9J5mVZ)) to your project.

Go to **Bannerbear Plugin**.

![go to bb plugin](https://user-images.githubusercontent.com/49879122/202170742-cd04650c-5b9e-4d6c-bbe4-64d00a5a4e3f.png)


Select **”Add New”** and paste your Bannerbear Project [API Key](https://www.bannerbear.com/help/articles/64-where-do-i-get-my-api-key/).  

![api key](https://user-images.githubusercontent.com/49879122/202170565-381713de-713a-4d13-8bc6-0044618cd272.png)

Select from your list of templates.

![template list](https://user-images.githubusercontent.com/49879122/202170481-c504c6eb-82aa-40e9-85fa-ef7f76479753.png)


This will automatically create and grab a [Signed URL Base](https://www.bannerbear.com/help/articles/179-generate-images-using-signed-urls/) from the template, and all available template modifications will be listed.

## Configuration

Each API modifiable layer will have a dropdown menu that can be mapped with fields from the WordPress site. Select layers that you want to modify and choose where you want to apply the template/Signed URL.

<img width="1403" alt="config - field mapping" src="https://user-images.githubusercontent.com/49879122/202156606-bcea9115-1671-4b1c-b22d-e6c78c9426ab.png">

It can also be embedded as a shortcode/snippet or added as a Block on WordPress pages.  

**Embed**

<img width="1430" alt="Screenshot 2022-11-16 at 7 01 35 PM" src="https://user-images.githubusercontent.com/49879122/202163755-011531db-5a08-42bc-937e-839aba0a93ec.png">

**Block** 
<img width="1429" alt="Screenshot 2022-11-16 at 8 01 22 PM" src="https://user-images.githubusercontent.com/49879122/202175799-6f66a67c-8940-4ff6-8e8c-a9c3cd05092b.png">


## Examples

**Display on Posts/Pages (after Content)**

<img width="851" alt="posts preview" src="https://user-images.githubusercontent.com/49879122/202168459-4adc353f-386f-4454-b4bb-d7bafcdc003e.png">

**Use as Open Graph Image** 

<img width="657" alt="twitter preview copy" src="https://user-images.githubusercontent.com/49879122/202169570-6bd6f498-ec92-4ca3-84b8-587b62146792.png">
