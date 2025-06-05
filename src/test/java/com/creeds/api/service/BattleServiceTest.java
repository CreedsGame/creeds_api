package com.creeds.api.service;

import com.creeds.api.model.Battle;
import com.creeds.api.model.Character;
import com.creeds.api.repository.BattleRepository;
import com.creeds.api.repository.CharacterRepository;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.util.Optional;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class BattleServiceTest {
    
    @Mock
    private BattleRepository battleRepository;
    
    @Mock
    private CharacterRepository characterRepository;
    
    private BattleService battleService;
    
    @BeforeEach
    void setUp() {
        battleService = new BattleService(battleRepository, characterRepository);
    }
    
    @Test
    void executeBattle_Success() {
        // Create test characters
        Character fighter = new Character();
        fighter.setName("fighter");
        fighter.setStrength(15);
        fighter.setEndurance(10);
        fighter.setInitiative(12);
        fighter.setLife(150);
        
        Character opponent = new Character();
        opponent.setName("opponent");
        opponent.setStrength(12);
        opponent.setEndurance(12);
        opponent.setInitiative(10);
        opponent.setLife(150);
        
        when(characterRepository.findByName("fighter")).thenReturn(Optional.of(fighter));
        when(characterRepository.findByName("opponent")).thenReturn(Optional.of(opponent));
        when(battleRepository.save(any())).thenAnswer(i -> i.getArgument(0));
        
        Battle battle = battleService.executeBattle("fighter", "opponent");
        
        assertNotNull(battle);
        assertEquals("fighter", battle.getFighter().getName());
        assertEquals("opponent", battle.getOpponent().getName());
        assertNotNull(battle.getTurns());
        assertFalse(battle.getTurns().isEmpty());
        verify(battleRepository).save(any());
    }
    
    @Test
    void executeBattle_FighterNotFound() {
        when(characterRepository.findByName("fighter")).thenReturn(Optional.empty());
        
        assertThrows(RuntimeException.class, () -> 
            battleService.executeBattle("fighter", "opponent")
        );
        
        verify(battleRepository, never()).save(any());
    }
    
    @Test
    void executeBattle_OpponentNotFound() {
        Character fighter = new Character();
        fighter.setName("fighter");
        when(characterRepository.findByName("fighter")).thenReturn(Optional.of(fighter));
        when(characterRepository.findByName("opponent")).thenReturn(Optional.empty());
        
        assertThrows(RuntimeException.class, () -> 
            battleService.executeBattle("fighter", "opponent")
        );
        
        verify(battleRepository, never()).save(any());
    }
} 