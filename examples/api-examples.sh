#!/bin/bash

# Creeds Game API - Example Usage Script
# This script demonstrates how to use the Creeds Game API

BASE_URL="http://localhost:8080/api/v1"

echo "=== Creeds Game API Examples ==="
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Example 1: Create First Character
echo -e "${BLUE}1. Creating first character (warrior)...${NC}"
curl -X POST "${BASE_URL}/characters" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "name=warrior&password=123456" \
  -w "\n\n"
echo ""

# Wait a moment
sleep 1

# Example 2: Create Second Character
echo -e "${BLUE}2. Creating second character (mage)...${NC}"
curl -X POST "${BASE_URL}/characters" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "name=mage&password=123456" \
  -w "\n\n"
echo ""

# Wait a moment
sleep 1

# Example 3: Get Character Information
echo -e "${BLUE}3. Getting warrior character information...${NC}"
curl -X GET "${BASE_URL}/characters/warrior" \
  -w "\n\n"
echo ""

# Example 4: Get All Characters
echo -e "${BLUE}4. Getting all characters...${NC}"
curl -X GET "${BASE_URL}/characters" \
  -w "\n\n"
echo ""

# Example 5: Login Character
echo -e "${BLUE}5. Logging in as warrior...${NC}"
curl -X POST "${BASE_URL}/characters/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "name=warrior&password=123456" \
  -w "\n\n"
echo ""

# Example 6: Execute Battle
echo -e "${YELLOW}6. Executing battle between warrior and mage...${NC}"
curl -X POST "${BASE_URL}/battles" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "fighter=warrior&opponent=mage" \
  -w "\n\n"
echo ""

echo -e "${GREEN}=== Examples completed! ===${NC}"
echo ""
echo "Access Swagger UI at: ${BASE_URL}/swagger-ui.html"
echo "Access H2 Console at: ${BASE_URL}/h2-console"

