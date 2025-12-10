# Creeds Game API - Example Usage Script (PowerShell)
# This script demonstrates how to use the Creeds Game API

$BaseUrl = "http://localhost:8080/api/v1"

Write-Host "=== Creeds Game API Examples ===" -ForegroundColor Cyan
Write-Host ""

# Example 1: Create First Character
Write-Host "1. Creating first character (warrior)..." -ForegroundColor Blue
$body = @{
    name = "warrior"
    password = "123456"
}
Invoke-RestMethod -Uri "$BaseUrl/characters" -Method Post -Body $body -ContentType "application/x-www-form-urlencoded" | ConvertTo-Json -Depth 10
Write-Host ""

Start-Sleep -Seconds 1

# Example 2: Create Second Character
Write-Host "2. Creating second character (mage)..." -ForegroundColor Blue
$body = @{
    name = "mage"
    password = "123456"
}
Invoke-RestMethod -Uri "$BaseUrl/characters" -Method Post -Body $body -ContentType "application/x-www-form-urlencoded" | ConvertTo-Json -Depth 10
Write-Host ""

Start-Sleep -Seconds 1

# Example 3: Get Character Information
Write-Host "3. Getting warrior character information..." -ForegroundColor Blue
Invoke-RestMethod -Uri "$BaseUrl/characters/warrior" -Method Get | ConvertTo-Json -Depth 10
Write-Host ""

# Example 4: Get All Characters
Write-Host "4. Getting all characters..." -ForegroundColor Blue
Invoke-RestMethod -Uri "$BaseUrl/characters" -Method Get | ConvertTo-Json -Depth 10
Write-Host ""

# Example 5: Login Character
Write-Host "5. Logging in as warrior..." -ForegroundColor Blue
$body = @{
    name = "warrior"
    password = "123456"
}
$loginResult = Invoke-RestMethod -Uri "$BaseUrl/characters/login" -Method Post -Body $body -ContentType "application/x-www-form-urlencoded"
Write-Host "Login result: $loginResult"
Write-Host ""

# Example 6: Execute Battle
Write-Host "6. Executing battle between warrior and mage..." -ForegroundColor Yellow
$body = @{
    fighter = "warrior"
    opponent = "mage"
}
Invoke-RestMethod -Uri "$BaseUrl/battles" -Method Post -Body $body -ContentType "application/x-www-form-urlencoded" | ConvertTo-Json -Depth 10
Write-Host ""

Write-Host "=== Examples completed! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Access Swagger UI at: $BaseUrl/swagger-ui.html"
Write-Host "Access H2 Console at: $BaseUrl/h2-console"

