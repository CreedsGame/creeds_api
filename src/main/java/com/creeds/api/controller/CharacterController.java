package com.creeds.api.controller;

import com.creeds.api.model.Character;
import com.creeds.api.service.CharacterService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.tags.Tag;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/characters")
@RequiredArgsConstructor
@Tag(name = "Character Management", description = "APIs for managing game characters")
public class CharacterController {
    
    private final CharacterService characterService;
    
    @PostMapping
    @Operation(summary = "Create a new character", description = "Creates a new character with the given name and password")
    public ResponseEntity<Character> createCharacter(
            @Parameter(description = "Character name") @RequestParam(required = true) String name,
            @Parameter(description = "Character password") @RequestParam(required = true) String password) {
        return ResponseEntity.ok(characterService.createCharacter(name, password));
    }
    
    @GetMapping("/{name}")
    @Operation(summary = "Get character by name", description = "Retrieves character information by name")
    public ResponseEntity<Character> getCharacter(
            @Parameter(description = "Character name") @PathVariable String name) {
        return ResponseEntity.ok(characterService.getCharacter(name));
    }
    
    @GetMapping
    @Operation(summary = "Get all characters", description = "Retrieves a list of all characters")
    public ResponseEntity<List<Character>> getAllCharacters() {
        return ResponseEntity.ok(characterService.getAllCharacters());
    }
    
    @PostMapping("/login")
    @Operation(summary = "Login character", description = "Validates character credentials")
    public ResponseEntity<Boolean> login(
            @Parameter(description = "Character name") @RequestParam(required = true) String name,
            @Parameter(description = "Character password") @RequestParam(required = true) String password) {
        return ResponseEntity.ok(characterService.validateLogin(name, password));
    }
} 