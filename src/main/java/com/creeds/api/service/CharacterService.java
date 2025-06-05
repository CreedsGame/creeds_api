package com.creeds.api.service;

import com.creeds.api.model.Character;
import com.creeds.api.repository.CharacterRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
@RequiredArgsConstructor
public class CharacterService {
    
    private final CharacterRepository characterRepository;
    
    @Transactional
    public Character createCharacter(String name, String password) {
        if (characterRepository.existsByName(name)) {
            throw new RuntimeException("Character name already exists");
        }
        
        Character character = new Character();
        character.setName(name);
        character.setPassword(password); // In a real application, this should be hashed
        return characterRepository.save(character);
    }
    
    public Character getCharacter(String name) {
        return characterRepository.findByName(name)
                .orElseThrow(() -> new RuntimeException("Character not found"));
    }
    
    public List<Character> getAllCharacters() {
        return characterRepository.findAll();
    }
    
    public boolean validateLogin(String name, String password) {
        return characterRepository.findByName(name)
                .map(character -> character.getPassword().equals(password))
                .orElse(false);
    }
} 