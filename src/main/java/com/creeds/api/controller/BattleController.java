package com.creeds.api.controller;

import com.creeds.api.model.Battle;
import com.creeds.api.service.BattleService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.tags.Tag;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/battles")
@RequiredArgsConstructor
@Tag(name = "Battle Management", description = "APIs for managing game battles")
public class BattleController {
    
    private final BattleService battleService;
    
    @PostMapping
    @Operation(summary = "Execute a battle", description = "Executes a battle between two characters and returns the result")
    public ResponseEntity<Battle> executeBattle(
            @Parameter(description = "Fighter character name") @RequestParam(required = true) String fighter,
            @Parameter(description = "Opponent character name") @RequestParam(required = true) String opponent) {
        return ResponseEntity.ok(battleService.executeBattle(fighter, opponent));
    }
} 