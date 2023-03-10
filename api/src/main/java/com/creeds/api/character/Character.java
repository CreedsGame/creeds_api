package com.creeds.api.character;

public class Character {

    private Integer id;
    private String name;


    public Character() {
    }

    public Character(Integer id, String name) {
        this.id = id;
        this.name = name;
    }

    public Integer getId() {
        return this.id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getName() {
        return this.name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Character id(Integer id) {
        setId(id);
        return this;
    }

    public Character name(String name) {
        setName(name);
        return this;
    }

    @Override
    public String toString() {
        return "{" +
            " id='" + getId() + "'" +
            ", name='" + getName() + "'" +
            "}";
    }

    
}
