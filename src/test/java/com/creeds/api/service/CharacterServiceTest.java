package com.creeds.api.service;

import com.creeds.api.model.Character;
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
class CharacterServiceTest {
    
    @Mock
    private CharacterRepository characterRepository;
    
    private CharacterService characterService;
    
    @BeforeEach
    void setUp() {
        characterService = new CharacterService(characterRepository);
    }
    
    @Test
    void createCharacter_Success() {
        when(characterRepository.existsByName(any())).thenReturn(false);
        when(characterRepository.save(any())).thenAnswer(i -> i.getArgument(0));
        
        Character character = characterService.createCharacter("test", "password");
        
        assertNotNull(character);
        assertEquals("test", character.getName());
        assertEquals("password", character.getPassword());
        verify(characterRepository).save(any());
    }
    
    @Test
    void createCharacter_DuplicateName() {
        when(characterRepository.existsByName(any())).thenReturn(true);
        
        assertThrows(RuntimeException.class, () -> 
            characterService.createCharacter("test", "password")
        );
        
        verify(characterRepository, never()).save(any());
    }
    
    @Test
    void getCharacter_Success() {
        Character character = new Character();
        character.setName("test");
        when(characterRepository.findByName("test")).thenReturn(Optional.of(character));
        
        Character result = characterService.getCharacter("test");
        
        assertNotNull(result);
        assertEquals("test", result.getName());
    }
    
    @Test
    void getCharacter_NotFound() {
        when(characterRepository.findByName(any())).thenReturn(Optional.empty());
        
        assertThrows(RuntimeException.class, () -> 
            characterService.getCharacter("test")
        );
    }
    
    @Test
    void validateLogin_Success() {
        Character character = new Character();
        character.setName("test");
        character.setPassword("password");
        when(characterRepository.findByName("test")).thenReturn(Optional.of(character));
        
        boolean result = characterService.validateLogin("test", "password");
        
        assertTrue(result);
    }
    
    @Test
    void validateLogin_InvalidPassword() {
        Character character = new Character();
        character.setName("test");
        character.setPassword("password");
        when(characterRepository.findByName("test")).thenReturn(Optional.of(character));
        
        boolean result = characterService.validateLogin("test", "wrong");
        
        assertFalse(result);
    }
} 