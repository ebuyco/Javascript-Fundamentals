getInitialState: function() {
    return {value: 3};
  },
  handleChange: function(event) {
    this.setState({value: event.target.value});
  },
  render: function() {
    return (
      <input 
        id="typeinp" 
        type="range" 
        min="0" max="5" 
        value={this.state.value} 
        onChange={this.handleChange}
        step="1"/>
    );
  }